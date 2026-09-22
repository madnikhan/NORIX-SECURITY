<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Models\Guard;
use App\Models\Invoice;
use App\Models\Shift;
use App\Models\Timesheet;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OpsStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('On site now', Shift::query()
                ->where('status', 'published')
                ->whereHas('attendancePunches', fn ($q) => $q->where('type', 'clock_in'))
                ->whereDoesntHave('attendancePunches', fn ($q) => $q->where('type', 'clock_out'))
                ->count()),
            Stat::make('Open applications', Application::query()->whereIn('status', ['submitted', 'under_review'])->count()),
            Stat::make('Timesheets to review', Timesheet::query()->where('status', 'submitted')->count()),
            Stat::make('Licences expiring <60d', Guard::query()->whereDate('sia_expiry', '<=', now()->addDays(60))->count()),
            Stat::make('Unpaid invoices', Invoice::query()->where('status', '!=', 'paid')->count()),
        ];
    }
}
