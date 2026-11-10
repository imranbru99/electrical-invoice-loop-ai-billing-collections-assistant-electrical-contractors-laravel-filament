<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\JobTicket;
use App\Models\Payment;
use App\Models\PaymentReminder;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BillingStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $openBalance = (float) Invoice::query()->sum('balance_due');
        $collected = (float) Payment::query()->sum('amount');
        $draftReminders = PaymentReminder::query()->where('status', 'draft')->count();
        $openJobs = JobTicket::query()->whereIn('status', ['scheduled', 'in_progress'])->count();

        return [
            Stat::make('Open Balance', '$'.number_format($openBalance, 2))
                ->description('AR awaiting collection')
                ->color('warning'),
            Stat::make('Collected', '$'.number_format($collected, 2))
                ->description('Payments recorded')
                ->color('success'),
            Stat::make('Draft Reminders', (string) $draftReminders)
                ->description('Awaiting human approval')
                ->color('primary'),
            Stat::make('Open Job Tickets', (string) $openJobs)
                ->description('Field work in flight')
                ->color('info'),
        ];
    }
}
