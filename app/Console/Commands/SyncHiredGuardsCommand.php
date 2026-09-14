<?php

namespace App\Console\Commands;

use App\Actions\HireApplication;
use App\Models\Application;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('norix:sync-hired-guards')]
#[Description('Create or refresh active Guard roster rows for all hired applications')]
class SyncHiredGuardsCommand extends Command
{
    public function handle(HireApplication $hireApplication): int
    {
        $applications = Application::query()
            ->with('candidate')
            ->where('status', 'hired')
            ->orderBy('id')
            ->get();

        if ($applications->isEmpty()) {
            $this->info('No hired applications found.');

            return self::SUCCESS;
        }

        $synced = 0;
        $failed = 0;

        foreach ($applications as $application) {
            try {
                $guard = $hireApplication($application);
                $this->line("Synced application #{$application->id} → guard #{$guard->id} ({$guard->full_name})");
                $synced++;
            } catch (Throwable $exception) {
                $this->error("Failed application #{$application->id}: {$exception->getMessage()}");
                $failed++;
            }
        }

        $this->info("Done. Synced {$synced}, failed {$failed}.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
