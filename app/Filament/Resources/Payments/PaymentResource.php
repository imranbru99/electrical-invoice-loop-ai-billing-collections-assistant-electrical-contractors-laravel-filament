<?php

namespace App\Filament\Resources\Payments;

use App\Filament\Concerns\ChecksModule;
use App\Filament\Resources\Payments\Pages\ManagePayments;
use App\Models\AuditLog;
use App\Models\Invoice;
use App\Models\Payment;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    use ChecksModule;

    protected static ?string $model = Payment::class;

    protected static string $moduleKey = 'payments_reconciliation';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('invoice_id')
                ->relationship('invoice', 'invoice_number')
                ->searchable()
                ->preload()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $invoice = Invoice::find($state);
                    if ($invoice) {
                        $set('customer_id', $invoice->customer_id);
                    }
                }),
            Select::make('customer_id')->relationship('customer', 'name')->searchable()->preload()->required(),
            TextInput::make('amount')->numeric()->required(),
            Select::make('method')->options([
                'check' => 'Check',
                'card' => 'Card',
                'ach' => 'ACH',
                'cash' => 'Cash',
                'other' => 'Other',
            ])->default('check'),
            TextInput::make('reference'),
            Select::make('status')->options([
                'recorded' => 'Recorded',
                'cleared' => 'Cleared',
                'failed' => 'Failed',
            ])->default('recorded'),
            DateTimePicker::make('paid_at'),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice.invoice_number')->label('Invoice'),
                TextColumn::make('customer.name'),
                TextColumn::make('amount')->money('USD'),
                TextColumn::make('method'),
                TextColumn::make('status')->badge(),
                TextColumn::make('paid_at')->dateTime(),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(function (Payment $record) {
                        static::reconcileInvoice($record);
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function reconcileInvoice(Payment $payment): void
    {
        $invoice = $payment->invoice;
        if (! $invoice) {
            return;
        }
        $paid = (float) $invoice->payments()->sum('amount');
        $balance = max(0, (float) $invoice->total - $paid);
        $status = $balance <= 0 ? 'paid' : ($paid > 0 ? 'partial' : $invoice->status);
        $invoice->update([
            'amount_paid' => $paid,
            'balance_due' => $balance,
            'status' => $status,
        ]);
        AuditLog::record(auth()->user(), 'payment.reconciled', $payment, [
            'invoice_id' => $invoice->id,
            'amount_paid' => $paid,
            'balance_due' => $balance,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePayments::route('/'),
        ];
    }
}
