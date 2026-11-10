<?php

namespace App\Services;

use App\Models\Invoice;

/**
 * Local equivalent of imrandevbd/laravel-unicode-pdf.
 * Returns a simple HTML invoice printable as PDF from the browser.
 */
class UnicodePdfService
{
    public function renderHtml(Invoice $invoice): string
    {
        $invoice->loadMissing(['customer', 'items']);
        $lines = $invoice->items->map(function ($item) {
            return sprintf(
                '<tr><td>%s</td><td>%s</td><td>$%s</td><td>$%s</td></tr>',
                e($item->description),
                e((string) $item->quantity),
                number_format((float) $item->unit_price, 2),
                number_format((float) $item->line_total, 2),
            );
        })->implode('');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Invoice {$invoice->invoice_number}</title>
<style>
body{font-family:DejaVu Sans,Arial,sans-serif;color:#111;margin:40px}
table{width:100%;border-collapse:collapse;margin-top:24px}
th,td{border:1px solid #ddd;padding:8px;text-align:left}
th{background:#f3f4f6}
.meta{margin-top:12px;color:#444}
</style>
</head>
<body>
<h1>ElectricalInvoiceLoop</h1>
<h2>Invoice {$invoice->invoice_number}</h2>
<p class="meta">Customer: {$invoice->customer->name}<br>
Status: {$invoice->status}<br>
Issue: {$invoice->issue_date} · Due: {$invoice->due_date}</p>
<table>
<thead><tr><th>Description</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead>
<tbody>{$lines}</tbody>
</table>
<p><strong>Subtotal:</strong> \${$invoice->subtotal}<br>
<strong>Tax:</strong> \${$invoice->tax_amount}<br>
<strong>Total:</strong> \${$invoice->total}<br>
<strong>Balance Due:</strong> \${$invoice->balance_due}</p>
</body>
</html>
HTML;
    }
}
