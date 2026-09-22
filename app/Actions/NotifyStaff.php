<?php

namespace App\Actions;

use App\Models\Guard;
use App\Models\StaffNotification;

class NotifyStaff
{
    /**
     * @param  array<string, mixed>|null  $data
     */
    public function __invoke(Guard $guard, string $type, string $title, ?string $body = null, ?array $data = null): StaffNotification
    {
        return StaffNotification::query()->create([
            'guard_id' => $guard->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);
    }
}
