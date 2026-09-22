<?php

namespace App\Filament\Widgets;

use App\Models\AttendancePunch;
use App\Models\Shift;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class OnSiteNowWidget extends TableWidget
{
    protected static ?string $heading = 'On site now';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Shift::query()
                    ->with(['site', 'assignedGuard', 'clockInPunch'])
                    ->where('status', 'published')
                    ->whereHas('attendancePunches', fn (Builder $q) => $q->where('type', AttendancePunch::TYPE_CLOCK_IN))
                    ->whereDoesntHave('attendancePunches', fn (Builder $q) => $q->where('type', AttendancePunch::TYPE_CLOCK_OUT))
                    ->latest('starts_at')
            )
            ->columns([
                TextColumn::make('assignedGuard.full_name')->label('Guard'),
                TextColumn::make('site.name')->label('Site'),
                TextColumn::make('clockInPunch.punched_at')->label('Clocked in')->dateTime(),
                TextColumn::make('ends_at')->label('Shift ends')->dateTime(),
            ])
            ->paginated(false);
    }
}
