<?php

namespace App\Filament\Resources\PaymentReminders;

use App\Filament\Concerns\ChecksModule;
use App\Filament\Resources\PaymentReminders\Pages\ManagePaymentReminders;
use App\Models\AuditLog;
use App\Models\PaymentReminder;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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

class PaymentReminderResource extends Resource
{
    use ChecksModule;

    protected static ?string $model = PaymentReminder::class;

    protected static string $moduleKey = 'collections_reminders';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('invoice_id')->relationship('invoice', 'invoice_number')->searchable()->preload()->required(),
            Select::make('customer_id')->relationship('customer', 'name')->searchable()->preload()->required(),
            Select::make('channel')->options([
                'email' => 'Email',
                'sms' => 'SMS',
            ])->default('email'),
            Select::make('status')->options([
                'draft' => 'Draft',
                'approved' => 'Approved',
                'sent' => 'Sent',
                'blocked' => 'Blocked (no consent)',
            ])->default('draft'),
            TextInput::make('subject'),
            Textarea::make('body')->required()->columnSpanFull()->rows(8),
            Toggle::make('ai_generated'),
            Toggle::make('consent_verified'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice.invoice_number'),
                TextColumn::make('customer.name'),
                TextColumn::make('channel')->badge(),
                TextColumn::make('status')->badge(),
                IconColumn::make('ai_generated')->boolean()->label('AI'),
                IconColumn::make('consent_verified')->boolean()->label('Consent'),
                TextColumn::make('sent_at')->dateTime(),
            ])
            ->recordActions([
                Action::make('approveSend')
                    ->label('Approve & Send')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->requiresConfirmation()
                    ->action(function (PaymentReminder $record) {
                        if (! $record->consent_verified) {
                            $record->update(['status' => 'blocked']);
                            Notification::make()->title('Blocked — messaging consent missing')->danger()->send();
                            AuditLog::record(auth()->user(), 'reminder.blocked_no_consent', $record);

                            return;
                        }
                        $record->update([
                            'status' => 'sent',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                            'sent_at' => now(),
                        ]);
                        AuditLog::record(auth()->user(), 'reminder.approved_sent', $record);
                        Notification::make()->title('Reminder approved and marked sent')->success()->send();
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
            'index' => ManagePaymentReminders::route('/'),
        ];
    }
}
