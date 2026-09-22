<?php

namespace App\Console\Commands;

use App\Actions\NotifyStaff;
use App\Models\Guard;
use App\Models\StaffNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('norix:send-sia-expiry-alerts')]
#[Description('Alert staff when SIA licences expire in 60, 30, or 14 days')]
class SendSiaExpiryAlertsCommand extends Command
{
    public function handle(NotifyStaff $notifyStaff): int
    {
        $thresholds = [60, 30, 14];
        $sent = 0;

        foreach ($thresholds as $days) {
            $target = now()->addDays($days)->toDateString();

            $guards = Guard::query()
                ->where('is_active', true)
                ->whereDate('sia_expiry', $target)
                ->get();

            foreach ($guards as $guard) {
                $already = StaffNotification::query()
                    ->where('guard_id', $guard->id)
                    ->where('type', 'sia_expiry')
                    ->where('created_at', '>=', now()->subDay())
                    ->where('data->days', $days)
                    ->exists();

                if ($already) {
                    continue;
                }

                $notifyStaff(
                    $guard,
                    'sia_expiry',
                    "SIA licence expires in {$days} days",
                    'Renew before '.$guard->sia_expiry->format('j M Y').' to stay deployable.',
                    ['days' => $days, 'sia_expiry' => $guard->sia_expiry->toDateString()],
                );
                $sent++;
            }
        }

        $this->info("Sent {$sent} SIA expiry alert(s).");

        return self::SUCCESS;
    }
}
