<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['site_id', 'guard_id', 'starts_at', 'ends_at', 'status', 'notes'])]
class Shift extends Model
{
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function assignedGuard(): BelongsTo
    {
        return $this->belongsTo(Guard::class, 'guard_id');
    }

    public function attendancePunches(): HasMany
    {
        return $this->hasMany(AttendancePunch::class)->orderBy('punched_at');
    }

    public function timesheet(): HasOne
    {
        return $this->hasOne(Timesheet::class);
    }

    public function clockInPunch(): HasOne
    {
        return $this->hasOne(AttendancePunch::class)
            ->where('type', AttendancePunch::TYPE_CLOCK_IN)
            ->latestOfMany('punched_at');
    }

    public function clockOutPunch(): HasOne
    {
        return $this->hasOne(AttendancePunch::class)
            ->where('type', AttendancePunch::TYPE_CLOCK_OUT)
            ->latestOfMany('punched_at');
    }

    public function isOpenOnSite(): bool
    {
        return $this->clockInPunch()->exists() && ! $this->clockOutPunch()->exists();
    }
}
