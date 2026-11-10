<?php

namespace Database\Seeders;

use App\Models\AttendanceLog;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JobTicket;
use App\Models\ModuleSetting;
use App\Models\Payment;
use App\Models\PaymentReminder;
use App\Models\ServiceRequest;
use App\Models\Technician;
use App\Models\User;
use App\Services\AiDraftService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('modules') as $key => $meta) {
            ModuleSetting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'label' => $meta['label'],
                    'description' => $meta['description'] ?? '',
                    'enabled' => (bool) ($meta['enabled'] ?? false),
                    'is_core' => (bool) ($meta['core'] ?? false),
                ]
            );
        }

        $users = [
            ['name' => 'Admin User', 'email' => 'admin@electricalinvoiceloop.test', 'role' => 'admin'],
            ['name' => 'Owner Lead', 'email' => 'owner@electricalinvoiceloop.test', 'role' => 'owner'],
            ['name' => 'Office Staff', 'email' => 'staff@electricalinvoiceloop.test', 'role' => 'staff'],
            ['name' => 'Field Tech', 'email' => 'tech@electricalinvoiceloop.test', 'role' => 'technician'],
            ['name' => 'Demo Customer', 'email' => 'customer@electricalinvoiceloop.test', 'role' => 'customer'],
        ];

        foreach ($users as $data) {
            User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'phone' => '555-010'.random_int(0, 9),
                    'is_active' => true,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }

        $admin = User::query()->where('email', 'admin@electricalinvoiceloop.test')->first();
        $techUser = User::query()->where('email', 'tech@electricalinvoiceloop.test')->first();

        $tech = Technician::query()->updateOrCreate(
            ['email' => 'tech@electricalinvoiceloop.test'],
            [
                'user_id' => $techUser?->id,
                'name' => 'Jordan Reyes',
                'phone' => '555-0142',
                'license_number' => 'EL-TX-44821',
                'specialty' => 'Residential panel upgrades',
                'is_available' => true,
                'slug' => 'jordan-reyes',
            ]
        );

        $customers = [
            [
                'name' => 'Ava Thompson',
                'email' => 'ava.thompson@example.com',
                'phone' => '555-2201',
                'company' => null,
                'address_line' => '418 Maple Ridge Dr',
                'city' => 'Austin',
                'state' => 'TX',
                'zip' => '78745',
                'sms_consent' => true,
                'email_consent' => true,
                'slug' => 'ava-thompson',
            ],
            [
                'name' => 'Summit Dental Group',
                'email' => 'ap@summitdental.example',
                'phone' => '555-8830',
                'company' => 'Summit Dental Group',
                'address_line' => '90 Commerce Blvd',
                'city' => 'Round Rock',
                'state' => 'TX',
                'zip' => '78664',
                'sms_consent' => false,
                'email_consent' => true,
                'slug' => 'summit-dental-group',
            ],
            [
                'name' => 'Chris Patel',
                'email' => 'chris.patel@example.com',
                'phone' => '555-4419',
                'company' => null,
                'address_line' => '12 Cedar Hollow Ln',
                'city' => 'Cedar Park',
                'state' => 'TX',
                'zip' => '78613',
                'sms_consent' => true,
                'email_consent' => true,
                'slug' => 'chris-patel',
            ],
        ];

        $customerModels = [];
        foreach ($customers as $c) {
            $customerModels[] = Customer::query()->updateOrCreate(['email' => $c['email']], $c);
        }

        $sr = ServiceRequest::query()->updateOrCreate(
            ['slug' => 'panel-upgrade-ava'],
            [
                'customer_id' => $customerModels[0]->id,
                'title' => '200A panel upgrade + EV charger circuit',
                'description' => 'Homeowner needs panel upgrade and Level 2 EV charger install.',
                'priority' => 'high',
                'status' => 'completed',
                'source' => 'web',
                'requested_at' => now()->subDays(10),
            ]
        );

        $ticket = JobTicket::query()->updateOrCreate(
            ['ticket_number' => 'JT-10041'],
            [
                'service_request_id' => $sr->id,
                'customer_id' => $customerModels[0]->id,
                'technician_id' => $tech->id,
                'title' => 'Panel upgrade + EV circuit',
                'work_summary' => 'Installed 200A panel, ran 60A EV circuit, inspected and labeled.',
                'status' => 'completed',
                'labor_hours' => 6.5,
                'materials_cost' => 840.00,
                'labor_rate' => 110.00,
                'scheduled_at' => now()->subDays(3),
                'completed_at' => now()->subDays(2),
                'slug' => 'jt-10041-panel-upgrade',
            ]
        );

        JobTicket::query()->updateOrCreate(
            ['ticket_number' => 'JT-10058'],
            [
                'customer_id' => $customerModels[1]->id,
                'technician_id' => $tech->id,
                'title' => 'Office lighting retrofit',
                'work_summary' => 'Replace fluorescent fixtures with LED troffers.',
                'status' => 'in_progress',
                'labor_hours' => 4,
                'materials_cost' => 620,
                'labor_rate' => 95,
                'scheduled_at' => now()->addDay(),
                'slug' => 'jt-10058-lighting',
            ]
        );

        if (! Invoice::query()->where('invoice_number', 'INV-DEMO-10041')->exists()) {
            $invoice = app(AiDraftService::class)->draftInvoiceFromJob($ticket, $admin);
            $invoice->update([
                'invoice_number' => 'INV-DEMO-10041',
                'status' => 'approved',
                'approved_at' => now()->subDay(),
                'approved_by' => $admin?->id,
                'due_date' => now()->subDays(2)->toDateString(),
            ]);
            $ticket->update(['status' => 'invoiced']);

            Payment::query()->create([
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'amount' => 500,
                'method' => 'check',
                'reference' => 'CHK-4412',
                'status' => 'cleared',
                'paid_at' => now()->subDay(),
                'notes' => 'Partial deposit',
            ]);
            $paid = 500;
            $invoice->update([
                'amount_paid' => $paid,
                'balance_due' => max(0, (float) $invoice->total - $paid),
                'status' => 'partial',
            ]);

            app(AiDraftService::class)->draftReminder($invoice->fresh(), 'email', $admin);
        }

        $manual = Invoice::query()->updateOrCreate(
            ['invoice_number' => 'INV-DEMO-10002'],
            [
                'customer_id' => $customerModels[2]->id,
                'created_by' => $admin?->id,
                'status' => 'overdue',
                'subtotal' => 450,
                'tax_amount' => 37.13,
                'total' => 487.13,
                'amount_paid' => 0,
                'balance_due' => 487.13,
                'issue_date' => now()->subDays(40)->toDateString(),
                'due_date' => now()->subDays(25)->toDateString(),
                'ai_generated' => false,
                'notes' => 'Outlet repair + GFCI replacements',
                'slug' => 'inv-demo-10002',
            ]
        );
        if ($manual->items()->count() === 0) {
            InvoiceItem::query()->create([
                'invoice_id' => $manual->id,
                'description' => 'GFCI outlet replacements (3)',
                'quantity' => 3,
                'unit_price' => 150,
                'line_total' => 450,
            ]);
        }

        PaymentReminder::query()->firstOrCreate(
            [
                'invoice_id' => $manual->id,
                'channel' => 'email',
                'status' => 'draft',
            ],
            [
                'customer_id' => $manual->customer_id,
                'subject' => 'Past-due invoice INV-DEMO-10002',
                'body' => "Hi Chris,\n\nInvoice INV-DEMO-10002 for \$487.13 is past due. Please let us know a good time to settle.\n\n— ElectricalInvoiceLoop",
                'ai_generated' => true,
                'consent_verified' => true,
            ]
        );

        AttendanceLog::query()->firstOrCreate(
            [
                'technician_id' => $tech->id,
                'type' => 'in',
                'punched_at' => now()->subHours(5),
            ],
            ['device' => 'ZKTeco-Demo']
        );

        AuditLog::record($admin, 'seed.completed', null, ['app' => 'ElectricalInvoiceLoop']);
    }
}
