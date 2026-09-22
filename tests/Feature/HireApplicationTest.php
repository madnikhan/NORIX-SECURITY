<?php

namespace Tests\Feature;

use App\Actions\HireApplication;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Guard;
use App\Models\JobPosting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class HireApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_hiring_an_application_creates_an_active_guard_for_shifts(): void
    {
        Mail::fake();

        $candidate = Candidate::query()->create([
            'name' => 'Alex Mercer',
            'email' => 'alex.mercer@example.com',
            'phone' => '+44 7700 900111',
            'password' => 'password',
        ]);

        $job = JobPosting::query()->create([
            'title' => 'Door Supervisor',
            'slug' => 'door-supervisor-test',
            'location' => 'London',
            'description' => 'SIA door supervisor role.',
            'requirements' => ['SIA licence'],
            'is_active' => true,
        ]);

        $application = Application::query()->create([
            'candidate_id' => $candidate->id,
            'job_posting_id' => $job->id,
            'reference' => 'NX-TEST-HIRE-1',
            'sia_licence_number' => '12345678901234',
            'sia_expiry' => now()->addYear()->toDateString(),
            'status' => 'docs_verified',
            'status_history' => [
                ['status' => 'submitted', 'at' => now()->toIso8601String()],
            ],
        ]);

        $guard = app(HireApplication::class)($application, actorId: 1);

        $this->assertTrue($guard->is_active);
        $this->assertSame($candidate->email, $guard->email);
        $this->assertSame($candidate->name, $guard->full_name);
        $this->assertSame($application->id, $guard->application_id);
        $this->assertSame('hired', $application->fresh()->status);
        $this->assertTrue($application->fresh()->hasRosterGuard());

        $shiftOptions = Guard::query()
            ->where('is_active', true)
            ->orderBy('full_name')
            ->pluck('full_name', 'id');

        $this->assertTrue($shiftOptions->has($guard->id));
        $this->assertSame('Alex Mercer', $shiftOptions->get($guard->id));
    }

    public function test_hire_action_is_idempotent_and_reactivates_guard(): void
    {
        Mail::fake();

        $candidate = Candidate::query()->create([
            'name' => 'Jordan Lee',
            'email' => 'jordan.lee@example.com',
            'phone' => '+44 7700 900222',
            'password' => 'password',
        ]);

        $job = JobPosting::query()->create([
            'title' => 'Manned Guard',
            'slug' => 'manned-guard-test',
            'location' => 'Manchester',
            'description' => 'Static officer role.',
            'requirements' => ['SIA licence'],
            'is_active' => true,
        ]);

        $application = Application::query()->create([
            'candidate_id' => $candidate->id,
            'job_posting_id' => $job->id,
            'reference' => 'NX-TEST-HIRE-2',
            'sia_licence_number' => '98765432109876',
            'sia_expiry' => now()->addMonths(6)->toDateString(),
            'status' => 'hired',
            'status_history' => [
                ['status' => 'hired', 'at' => now()->toIso8601String()],
            ],
        ]);

        Guard::query()->create([
            'full_name' => 'Jordan Lee',
            'email' => 'jordan.lee@example.com',
            'phone' => '+44 7700 900222',
            'sia_licence_number' => '98765432109876',
            'sia_expiry' => now()->addMonths(6)->toDateString(),
            'application_id' => $application->id,
            'is_active' => false,
            'password' => 'password',
            'must_set_password' => false,
        ]);

        $guard = app(HireApplication::class)($application);

        $this->assertSame(1, Guard::query()->where('email', 'jordan.lee@example.com')->count());
        $this->assertTrue($guard->fresh()->is_active);
    }
}
