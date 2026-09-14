<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'full_name',
    'email',
    'phone',
    'sia_licence_number',
    'sia_expiry',
    'availability_notes',
    'application_id',
    'is_active',
])]
class Guard extends Model
{
    protected function casts(): array
    {
        return [
            'sia_expiry' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function isLicenceExpiringSoon(int $days = 60): bool
    {
        return $this->sia_expiry->lessThanOrEqualTo(now()->addDays($days));
    }
}
