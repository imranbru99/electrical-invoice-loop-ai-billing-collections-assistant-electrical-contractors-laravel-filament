<?php

namespace App\Services;

use App\Models\AiDraft;
use App\Models\AuditLog;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JobTicket;
use App\Models\PaymentReminder;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Local equivalent of imrandevbd/laravel-ai-hub for invoice/reminder drafting.
 */
class AiDraftService
{
    public function draftInvoiceFromJob(JobTicket $ticket, ?User $actor = null): Invoice
    {
        $labor = round(((float) $ticket->labor_hours) * ((float) $ticket->labor_rate), 2);
        $materials = (float) $ticket->materials_cost;
        $subtotal = $labor + $materials;
        $tax = round($subtotal * 0.0825, 2);
        $total = $subtotal + $tax;

        $invoice = Invoice::query()->create([
            'invoice_number' => 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(4)),
            'customer_id' => $ticket->customer_id,
            'job_ticket_id' => $ticket->id,
            'created_by' => $actor?->id,
            'status' => 'draft',
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total' => $total,
            'amount_paid' => 0,
            'balance_due' => $total,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(15)->toDateString(),
            'ai_generated' => true,
            'ai_draft_notes' => "AI drafted from job {$ticket->ticket_number}: labor {$ticket->labor_hours}h @ \${$ticket->labor_rate}, materials \${$materials}. Awaiting human approval.",
            'slug' => Str::slug('inv-'.$ticket->ticket_number.'-'.Str::random(4)),
        ]);

        InvoiceItem::query()->create([
            'invoice_id' => $invoice->id,
            'description' => 'Electrical labor — '.$ticket->title,
            'quantity' => max((float) $ticket->labor_hours, 1),
            'unit_price' => $ticket->labor_rate,
            'line_total' => $labor ?: (float) $ticket->labor_rate,
        ]);

        if ($materials > 0) {
            InvoiceItem::query()->create([
                'invoice_id' => $invoice->id,
                'description' => 'Materials & supplies',
                'quantity' => 1,
                'unit_price' => $materials,
                'line_total' => $materials,
            ]);
        }

        AiDraft::query()->create([
            'draftable_type' => Invoice::class,
            'draftable_id' => $invoice->id,
            'kind' => 'invoice',
            'status' => 'pending_approval',
            'payload' => json_encode([
                'summary' => $invoice->ai_draft_notes,
                'total' => $total,
            ]),
            'created_by' => $actor?->id,
        ]);

        AuditLog::record($actor, 'ai.draft_invoice', $invoice, [
            'job_ticket_id' => $ticket->id,
            'total' => $total,
        ]);

        return $invoice->load('items');
    }

    public function draftReminder(Invoice $invoice, string $channel = 'email', ?User $actor = null): PaymentReminder
    {
        $customer = $invoice->customer;
        $daysPast = $invoice->due_date ? now()->diffInDays($invoice->due_date, false) : 0;
        $tone = $daysPast > 0 ? 'firm but professional' : 'friendly';

        $subject = "Payment reminder for invoice {$invoice->invoice_number}";
        $body = "Hi {$customer->name},\n\n"
            ."This is a {$tone} reminder that invoice {$invoice->invoice_number} for \$"
            .number_format((float) $invoice->balance_due, 2)
            .' is '.($daysPast > 0 ? "{$daysPast} days past due" : 'coming due on '.$invoice->due_date).".\n\n"
            ."Please reply or pay at your earliest convenience. Thank you for choosing our electrical team.\n\n"
            .'— ElectricalInvoiceLoop Collections';

        $consent = $channel === 'sms' ? (bool) $customer->sms_consent : (bool) $customer->email_consent;

        $reminder = PaymentReminder::query()->create([
            'invoice_id' => $invoice->id,
            'customer_id' => $customer->id,
            'channel' => $channel,
            'status' => 'draft',
            'subject' => $subject,
            'body' => $body,
            'ai_generated' => true,
            'consent_verified' => $consent,
        ]);

        AiDraft::query()->create([
            'draftable_type' => PaymentReminder::class,
            'draftable_id' => $reminder->id,
            'kind' => 'reminder',
            'status' => 'pending_approval',
            'payload' => json_encode(['subject' => $subject, 'body' => $body, 'channel' => $channel]),
            'created_by' => $actor?->id,
        ]);

        AuditLog::record($actor, 'ai.draft_reminder', $reminder, [
            'invoice_id' => $invoice->id,
            'channel' => $channel,
            'consent' => $consent,
        ]);

        return $reminder;
    }
}
