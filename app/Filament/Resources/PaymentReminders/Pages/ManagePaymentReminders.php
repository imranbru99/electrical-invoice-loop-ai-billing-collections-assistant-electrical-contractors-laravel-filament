<?php

namespace App\Filament\Resources\PaymentReminders\Pages;

use App\Filament\Resources\PaymentReminders\PaymentReminderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePaymentReminders extends ManageRecords
{
    protected static string $resource = PaymentReminderResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
