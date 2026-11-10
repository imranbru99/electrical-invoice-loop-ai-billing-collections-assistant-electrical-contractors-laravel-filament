<?php

namespace App\Filament\Resources\AiDrafts;

use App\Filament\Concerns\ChecksModule;
use App\Filament\Resources\AiDrafts\Pages\ManageAiDrafts;
use App\Models\AiDraft;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AiDraftResource extends Resource
{
    use ChecksModule;

    protected static ?string $model = AiDraft::class;

    protected static string $moduleKey = 'ai_draft_assistant';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|\UnitEnum|null $navigationGroup = 'AI & Compliance';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'AI Drafts';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('kind')->disabled(),
            TextInput::make('status')->disabled(),
            Textarea::make('payload')->disabled()->columnSpanFull()->rows(10),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('kind'),
            TextEntry::make('status'),
            TextEntry::make('payload')->columnSpanFull(),
            TextEntry::make('creator.name')->label('Created by'),
            TextEntry::make('created_at')->dateTime(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kind')->badge(),
                TextColumn::make('status')->badge(),
                TextColumn::make('draftable_type')->label('Target')->limit(30),
                TextColumn::make('creator.name')->label('Created by'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAiDrafts::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
