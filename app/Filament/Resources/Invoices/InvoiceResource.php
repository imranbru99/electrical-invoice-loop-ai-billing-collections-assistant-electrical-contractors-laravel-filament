<?php

namespace App\Filament\Resources\Invoices;

use App\Filament\Concerns\ChecksModule;
use App\Filament\Resources\Invoices\Pages\ManageInvoices;
use App\Models\AuditLog;
use App\Models\Invoice;
use App\Services\AiDraftService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    use ChecksModule;

    protected static ?string $model = Invoice::class;

    protected static string $moduleKey = 'invoicing';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCurrencyDollar;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('invoice_number')->required()->unique(ignoreRecord: true),
            Select::make('customer_id')->relationship('customer', 'name')->searchable()->preload()->required(),
            Select::make('job_ticket_id')->relationship('jobTicket', 'ticket_number')->searchable()->preload(),
            Select::make('status')->options([
                'draft' => 'Draft',
                'pending_approval' => 'Pending Approval',
                'approved' => 'Approved',
                'sent' => 'Sent',
                'partial' => 'Partially Paid',
                'paid' => 'Paid',
                'overdue' => 'Overdue',
                'void' => 'Void',
            ])->default('draft'),
            TextInput::make('subtotal')->numeric(),
            TextInput::make('tax_amount')->numeric(),
            TextInput::make('total')->numeric(),
            TextInput::make('amount_paid')->numeric(),
            TextInput::make('balance_due')->numeric(),
            DatePicker::make('issue_date'),
            DatePicker::make('due_date'),
            Toggle::make('ai_generated'),
            Textarea::make('ai_draft_notes')->columnSpanFull(),
            Textarea::make('notes')->columnSpanFull(),
            TextInput::make('slug'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')->searchable()->sortable(),
                TextColumn::make('customer.name'),
                TextColumn::make('status')->badge(),
                TextColumn::make('total')->money('USD'),
                TextColumn::make('balance_due')->money('USD'),
                TextColumn::make('due_date')->date(),
                IconColumn::make('ai_generated')->boolean()->label('AI'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->visible(fn (Invoice $record) => in_array($record->status, ['draft', 'pending_approval'], true))
                    ->requiresConfirmation()
                    ->action(function (Invoice $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]);
                        AuditLog::record(auth()->user(), 'invoice.approved', $record);
                        Notification::make()->title('Invoice approved')->success()->send();
                    }),
                Action::make('aiReminder')
                    ->label('AI Reminder')
                    ->icon(Heroicon::OutlinedBellAlert)
                    ->visible(fn () => \App\Support\Modules::enabled('collections_reminders') && \App\Support\Modules::enabled('ai_draft_assistant'))
                    ->action(function (Invoice $record) {
                        $reminder = app(AiDraftService::class)->draftReminder($record, 'email', auth()->user());
                        Notification::make()
                            ->title('AI reminder drafted')
                            ->body('Status: '.$reminder->status.' · Consent: '.($reminder->consent_verified ? 'yes' : 'no'))
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageInvoices::route('/'),
        ];
    }
}
