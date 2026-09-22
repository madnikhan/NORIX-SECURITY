<?php

namespace App\Console\Commands;

use App\Actions\NotifyStaff;
use App\Models\Shift;
use App\Models\ShiftReminderLog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('norix:send-shift-reminders')]
#[Description('Notify staff of upcoming shifts (2h and 30m windows)')]
class SendShiftRemindersCommand extends Command
{
    public function handle(NotifyStaff $notifyStaff): int
    {
        $windows = [
            '2h' => [115, 125],
            '30m' => [25, 35],
        ];

        $sent = 0;

        foreach ($windows as $window => [$minMinutes, $maxMinutes]) {
            $from = now()->addMinutes($minMinutes);
            $to = now()->addMinutes($maxMinutes);

            $shifts = Shift::query()
                ->with(['assignedGuard', 'site'])
                ->where('status', 'published')
                ->whereBetween('starts_at', [$from, $to])
                ->get();

            foreach ($shifts as $shift) {
                if ($shift->assignedGuard === null) {
                    continue;
                }

                $exists = ShiftReminderLog::query()
                    ->where('shift_id', $shift->id)
                    ->where('window', $window)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $title = $window === '2h'
                    ? 'Shift in about 2 hours'
                    : 'Shift starts in about 30 minutes';

                $body = sprintf(
                    '%s at %s (%s)',
                    $shift->site?->name ?? 'Site',
                    $shift->starts_at->format('H:i'),
                    $shift->site?->address ?? '',
                );

                $notifyStaff($shift->assignedGuard, 'shift_reminder', $title, $body, [
                    'shift_id' => $shift->id,
                    'window' => $window,
                ]);

                ShiftReminderLog::query()->create([
                    'shift_id' => $shift->id,
                    'guard_id' => $shift->guard_id,
                    'window' => $window,
                    'sent_at' => now(),
                ]);

                $sent++;
            }
        }

        $this->info("Sent {$sent} shift reminder(s).");

        return self::SUCCESS;
    }
}
