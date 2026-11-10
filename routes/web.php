<?php

use App\Http\Controllers\MarketingController;
use App\Models\Invoice;
use App\Services\UnicodePdfService;
use App\Support\Modules;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'home'])->name('home');
Route::get('/features', [MarketingController::class, 'features'])->name('features');
Route::get('/workflow', [MarketingController::class, 'workflow'])->name('workflow');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('pricing');
Route::get('/modules', [MarketingController::class, 'modules'])->name('modules');
Route::get('/demo', [MarketingController::class, 'demo'])->name('demo');

Route::get('/invoices/{invoice}/pdf', function (Invoice $invoice, UnicodePdfService $pdf) {
    abort_unless(Modules::enabled('unicode_pdf'), 404);

    return response($pdf->renderHtml($invoice))->header('Content-Type', 'text/html; charset=UTF-8');
})->name('invoices.pdf');
