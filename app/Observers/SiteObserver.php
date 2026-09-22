<?php

namespace App\Observers;

use App\Actions\GeocodeAddress;
use App\Models\Site;
use Throwable;

class SiteObserver
{
    public function __construct(private GeocodeAddress $geocodeAddress) {}

    public function saving(Site $site): void
    {
        if (! $site->isDirty('address') && $site->hasCoordinates()) {
            return;
        }

        if (blank($site->address)) {
            return;
        }

        // Skip auto-geocode when admin already set coordinates for this save.
        if ($site->isDirty('latitude') || $site->isDirty('longitude')) {
            if ($site->hasCoordinates()) {
                $site->geocoded_at = now();
            }

            return;
        }

        try {
            $coords = ($this->geocodeAddress)($site->address);
        } catch (Throwable) {
            return;
        }

        if ($coords === null) {
            return;
        }

        $site->latitude = $coords['lat'];
        $site->longitude = $coords['lng'];
        $site->geocoded_at = now();

        if (blank($site->geofence_radius_meters)) {
            $site->geofence_radius_meters = (int) config('staff.geofence_default_radius_meters');
        }
    }
}
