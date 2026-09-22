<?php

return [
    'geofence_default_radius_meters' => (int) env('STAFF_GEOFENCE_RADIUS', 150),
    'max_gps_accuracy_meters' => (float) env('STAFF_MAX_GPS_ACCURACY', 100),
    'clock_early_minutes' => (int) env('STAFF_CLOCK_EARLY_MINUTES', 15),
    'clock_grace_minutes' => (int) env('STAFF_CLOCK_GRACE_MINUTES', 30),
    'clamp_hours_to_shift' => (bool) env('STAFF_CLAMP_HOURS_TO_SHIFT', true),
];
