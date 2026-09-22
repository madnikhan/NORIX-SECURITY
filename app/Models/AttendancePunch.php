<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'shift_id',
    'guard_id',
    'type',
    'punched_at',
    'lat',
    'lng',
    'accuracy_meters',
    'distance_meters',
    'within_geofence',
    'device_meta',
    'source',
])]
class AttendancePunch extends Model
{
    public const TYPE_CLOCK_IN = 'clock_in';

    public const TYPE_CLOCK_OUT = 'clock_out';

    protected function casts(): array
    {
        return [
            'punched_at' => 'datetime',
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
            'accuracy_meters' => 'decimal:2',
            'distance_meters' => 'decimal:2',
            'within_geofence' => 'boolean',
            'device_meta' => 'array',
        ];
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function assignedGuard(): BelongsTo
    {
        return $this->belongsTo(Guard::class, 'guard_id');
    }
}
