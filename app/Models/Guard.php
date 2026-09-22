<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable([
    'full_name',
    'email',
    'password',
    'phone',
    'sia_licence_number',
    'sia_expiry',
    'availability_notes',
    'default_hourly_rate',
    'application_id',
    'is_active',
    'must_set_password',
    'invite_token',
    'invite_sent_at',
    'last_login_at',
])]
#[Hidden(['password', 'remember_token', 'invite_token'])]
class Guard extends Authenticatable
{
    use Notifiable;

    protected function casts(): array
    {
        return [
            'sia_expiry' => 'date',
            'is_active' => 'boolean',
            'must_set_password' => 'boolean',
            'password' => 'hashed',
            'default_hourly_rate' => 'decimal:2',
            'invite_sent_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
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

    public function attendancePunches(): HasMany
    {
        return $this->hasMany(AttendancePunch::class);
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function staffConversations(): HasMany
    {
        return $this->hasMany(StaffConversation::class);
    }

    public function staffNotifications(): HasMany
    {
        return $this->hasMany(StaffNotification::class);
    }

    public function policyAcknowledgements(): HasMany
    {
        return $this->hasMany(PolicyAcknowledgement::class);
    }

    public function isLicenceExpiringSoon(int $days = 60): bool
    {
        return $this->sia_expiry !== null
            && $this->sia_expiry->lessThanOrEqualTo(now()->addDays($days));
    }

    public function issueInviteToken(): string
    {
        $token = Str::random(64);

        $this->forceFill([
            'invite_token' => hash('sha256', $token),
            'must_set_password' => true,
            'invite_sent_at' => now(),
        ])->save();

        return $token;
    }

    public static function findByInviteToken(string $plainToken): ?self
    {
        return static::query()
            ->where('invite_token', hash('sha256', $plainToken))
            ->where('is_active', true)
            ->first();
    }
}
