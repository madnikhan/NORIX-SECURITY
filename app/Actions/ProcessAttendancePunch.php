<?php

namespace App\Actions;

use App\Models\AttendancePunch;
use App\Models\Guard;
use App\Models\Shift;
use App\Models\Timesheet;
use App\Support\Geo;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class ProcessAttendancePunch
{
    /**
     * @param  array{lat: float, lng: float, accuracy?: float|null, device_meta?: array<string, mixed>|null}  $location
     */
    public function __invoke(Guard $guard, Shift $shift, string $type, array $location): AttendancePunch
    {
        if (! in_array($type, [AttendancePunch::TYPE_CLOCK_IN, AttendancePunch::TYPE_CLOCK_OUT], true)) {
            throw new InvalidArgumentException('Invalid punch type.');
        }

        if ((int) $shift->guard_id !== (int) $guard->id) {
            throw new RuntimeException('This shift is not assigned to you.');
        }

        if ($shift->status !== 'published') {
            throw new RuntimeException('This shift is not open for clocking.');
        }

        $shift->loadMissing('site');
        $site = $shift->site;

        if ($site === null || ! $site->hasCoordinates()) {
            throw new RuntimeException('This site has no geofence coordinates yet. Contact operations.');
        }

        $lat = (float) $location['lat'];
        $lng = (float) $location['lng'];
        $accuracy = isset($location['accuracy']) ? (float) $location['accuracy'] : null;
        $maxAccuracy = (float) config('staff.max_gps_accuracy_meters');

        if ($accuracy !== null && $accuracy > $maxAccuracy) {
            throw new RuntimeException('GPS accuracy is too low. Move outdoors or wait for a better signal.');
        }

        $distance = Geo::distanceMeters(
            $lat,
            $lng,
            (float) $site->latitude,
            (float) $site->longitude,
        );

        $radius = (int) ($site->geofence_radius_meters ?: config('staff.geofence_default_radius_meters'));

        if ($distance > $radius) {
            throw new RuntimeException(sprintf(
                'You are %.0fm from the site (allowed %dm). Move closer to clock.',
                $distance,
                $radius,
            ));
        }

        $now = now();
        $early = (int) config('staff.clock_early_minutes');
        $grace = (int) config('staff.clock_grace_minutes');
        $windowStart = $shift->starts_at->copy()->subMinutes($early);
        $windowEnd = $shift->ends_at->copy()->addMinutes($grace);

        if ($now->lt($windowStart) || $now->gt($windowEnd)) {
            throw new RuntimeException('Clocking is only allowed from '.$early.' minutes before the shift until '.$grace.' minutes after it ends.');
        }

        return DB::transaction(function () use ($guard, $shift, $type, $lat, $lng, $accuracy, $distance, $location, $now) {
            $existingIn = AttendancePunch::query()
                ->where('shift_id', $shift->id)
                ->where('type', AttendancePunch::TYPE_CLOCK_IN)
                ->lockForUpdate()
                ->first();

            $existingOut = AttendancePunch::query()
                ->where('shift_id', $shift->id)
                ->where('type', AttendancePunch::TYPE_CLOCK_OUT)
                ->lockForUpdate()
                ->first();

            if ($type === AttendancePunch::TYPE_CLOCK_IN) {
                if ($existingIn !== null) {
                    throw new RuntimeException('You have already clocked in for this shift.');
                }
            } else {
                if ($existingIn === null) {
                    throw new RuntimeException('Clock in before you clock out.');
                }
                if ($existingOut !== null) {
                    throw new RuntimeException('You have already clocked out for this shift.');
                }
            }

            $punch = AttendancePunch::query()->create([
                'shift_id' => $shift->id,
                'guard_id' => $guard->id,
                'type' => $type,
                'punched_at' => $now,
                'lat' => $lat,
                'lng' => $lng,
                'accuracy_meters' => $accuracy,
                'distance_meters' => round($distance, 2),
                'within_geofence' => true,
                'device_meta' => $location['device_meta'] ?? null,
                'source' => 'staff_app',
            ]);

            if ($type === AttendancePunch::TYPE_CLOCK_OUT) {
                $shift->loadMissing('site');
                $this->syncTimesheet($guard, $shift, $existingIn, $punch);
            }

            return $punch;
        });
    }

    private function syncTimesheet(Guard $guard, Shift $shift, AttendancePunch $clockIn, AttendancePunch $clockOut): void
    {
        $start = $clockIn->punched_at->copy();
        $end = $clockOut->punched_at->copy();

        if (config('staff.clamp_hours_to_shift')) {
            $start = $start->max($shift->starts_at);
            $end = $end->min($shift->ends_at);
        }

        $minutes = max(0, $start->diffInMinutes($end));
        $hours = round($minutes / 60, 2);
        $rate = $guard->default_hourly_rate ?? 0;

        Timesheet::query()->updateOrCreate(
            ['shift_id' => $shift->id],
            [
                'guard_id' => $guard->id,
                'site_id' => $shift->site_id,
                'client_id' => $shift->site?->client_id,
                'hours' => $hours,
                'hourly_rate' => $rate,
                'status' => 'submitted',
                'submitted_at' => now(),
                'notes' => 'Auto-generated from staff clock-in/out.',
            ],
        );

        if ($shift->status === 'published') {
            $shift->update(['status' => 'completed']);
        }
    }
}
