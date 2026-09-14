<?php

namespace App\Actions;

use App\Models\Application;
use App\Models\Guard;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class HireApplication
{
    public function __invoke(Application $application, ?int $actorId = null): Guard
    {
        return DB::transaction(function () use ($application, $actorId) {
            $application->loadMissing('candidate');

            $candidate = $application->candidate;

            if ($candidate === null) {
                throw new RuntimeException('Application #'.$application->id.' has no candidate.');
            }

            if (blank($candidate->email)) {
                throw new RuntimeException('Candidate for application #'.$application->id.' has no email.');
            }

            $history = $application->status_history ?? [];

            if ($application->status !== 'hired') {
                $history[] = [
                    'status' => 'hired',
                    'at' => now()->toIso8601String(),
                    'by' => $actorId,
                ];
            } elseif (! collect($history)->contains(fn ($entry) => ($entry['status'] ?? null) === 'hired' && array_key_exists('by', $entry))) {
                $history[] = [
                    'status' => 'hired',
                    'at' => now()->toIso8601String(),
                    'by' => $actorId,
                    'note' => 'roster_sync',
                ];
            }

            $application->update([
                'status' => 'hired',
                'status_history' => $history,
            ]);

            return Guard::query()->updateOrCreate(
                ['email' => $candidate->email],
                [
                    'full_name' => $candidate->name,
                    'phone' => $candidate->phone,
                    'sia_licence_number' => $application->sia_licence_number ?: 'PENDING',
                    'sia_expiry' => $application->sia_expiry?->toDateString() ?: now()->addYear()->toDateString(),
                    'application_id' => $application->id,
                    'is_active' => true,
                ]
            );
        });
    }
}
