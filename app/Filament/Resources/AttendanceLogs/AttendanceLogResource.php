<?php

namespace App\Filament\Resources\AttendanceLogs;

use App\Filament\Concerns\ChecksModule;
use App\Filament\Resources\AttendanceLogs\Pages\ManageAttendanceLogs;
use App\Models\AttendanceLog;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttendanceLogResource extends Resource
{
    use ChecksModule;

    protected static ?string $model = AttendanceLog::class;

    protected static string $moduleKey = 'attendance_hub';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|\UnitEnum|null $navigationGroup = 'Field Ops';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('technician_id')->relationship('technician', 'name')->searchable()->preload()->required(),
            DateTimePicker::make('punched_at')->required(),
            Select::make('type')->options(['in' => 'Clock In', 'out' => 'Clock Out'])->default('in'),
            TextInput::make('device'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('technician.name'),
                TextColumn::make('punched_at')->dateTime()->sortable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('device'),
            ])
            ->recordActions([
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
            'index' => ManageAttendanceLogs::route('/'),
        ];
    }
}
