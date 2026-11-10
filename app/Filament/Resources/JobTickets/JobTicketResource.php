<?php

namespace App\Filament\Resources\JobTickets;

use App\Filament\Concerns\ChecksModule;
use App\Filament\Resources\JobTickets\Pages\ManageJobTickets;
use App\Models\JobTicket;
use App\Services\AiDraftService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JobTicketResource extends Resource
{
    use ChecksModule;

    protected static ?string $model = JobTicket::class;

    protected static string $moduleKey = 'job_tickets';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'Field Ops';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('ticket_number')->required()->unique(ignoreRecord: true),
            Select::make('customer_id')->relationship('customer', 'name')->searchable()->preload()->required(),
            Select::make('service_request_id')->relationship('serviceRequest', 'title')->searchable()->preload(),
            Select::make('technician_id')->relationship('technician', 'name')->searchable()->preload(),
            TextInput::make('title')->required(),
            Textarea::make('work_summary')->columnSpanFull(),
            Select::make('status')->options([
                'scheduled' => 'Scheduled',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
                'invoiced' => 'Invoiced',
            ])->default('scheduled'),
            TextInput::make('labor_hours')->numeric()->default(0),
            TextInput::make('labor_rate')->numeric()->default(95),
            TextInput::make('materials_cost')->numeric()->default(0),
            DateTimePicker::make('scheduled_at'),
            DateTimePicker::make('completed_at'),
            TextInput::make('slug'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_number')->searchable()->sortable(),
                TextColumn::make('title')->limit(36),
                TextColumn::make('customer.name'),
                TextColumn::make('technician.name'),
                TextColumn::make('status')->badge(),
                TextColumn::make('labor_hours'),
                TextColumn::make('materials_cost')->money('USD'),
            ])
            ->recordActions([
                Action::make('aiDraftInvoice')
                    ->label('AI Draft Invoice')
                    ->icon(Heroicon::OutlinedSparkles)
                    ->visible(fn () => \App\Support\Modules::enabled('ai_draft_assistant') && \App\Support\Modules::enabled('invoicing'))
                    ->action(function (JobTicket $record) {
                        $invoice = app(AiDraftService::class)->draftInvoiceFromJob($record, auth()->user());
                        $record->update(['status' => 'invoiced']);
                        Notification::make()
                            ->title('AI invoice draft created')
                            ->body($invoice->invoice_number.' · $'.number_format((float) $invoice->total, 2))
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
            'index' => ManageJobTickets::route('/'),
        ];
    }
}
