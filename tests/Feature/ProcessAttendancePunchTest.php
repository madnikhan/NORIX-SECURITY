<?php

namespace Tests\Feature;

use App\Actions\HireApplication;
use App\Actions\ProcessAttendancePunch;
use App\Mail\StaffInviteMail;
use App\Models\Application;
use App\Models\AttendancePunch;
use App\Models\Candidate;
use App\Models\Client;
use App\Models\Guard;
use App\Models\JobPosting;
use App\Models\Shift;
use App\Models\Site;
use App\Models\Timesheet;
use App\Support\Geo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProcessAttendancePunchTest extends TestCase
{
    use RefreshDatabase;

    public function test_clock_out_creates_timesheet_with_hours(): void
    {
        Http::fake();

        $client = Client::query()->create([
            'name' => 'Acme',
            'contact_name' => 'Ops',
            'email' => 'ops@acme.test',
            'phone' => '07000000000',
            'address' => '1 Test St',
            'portal_access' => false,
        ]);

        $site = Site::query()->create([
            'client_id' => $client->id,
            'name' => 'Warehouse',
            'address' => '1 Test Street, London',
            'latitude' => 51.5074,
            'longitude' => -0.1278,
            'geofence_radius_meters' => 200,
            'geocoded_at' => now(),
            'is_active' => true,
        ]);

        $guard = Guard::query()->create([
            'full_name' => 'Sam Guard',
            'email' => 'sam.guard@example.com',
            'phone' => '07000000001',
            'password' => 'password',
            'must_set_password' => false,
            'sia_licence_number' => '11112222333344',
            'sia_expiry' => now()->addYear(),
            'default_hourly_rate' => 15.50,
            'is_active' => true,
        ]);

        $shift = Shift::query()->create([
            'site_id' => $site->id,
            'guard_id' => $guard->id,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHours(3),
            'status' => 'published',
        ]);

        $process = app(ProcessAttendancePunch::class);

        $process($guard, $shift, AttendancePunch::TYPE_CLOCK_IN, [
            'lat' => 51.5074,
            'lng' => -0.1278,
            'accuracy' => 12,
        ]);

        $this->travel(90)->minutes();

        $process($guard, $shift->fresh(), AttendancePunch::TYPE_CLOCK_OUT, [
            'lat' => 51.5074,
            'lng' => -0.1278,
            'accuracy' => 10,
        ]);

        $timesheet = Timesheet::query()->where('shift_id', $shift->id)->first();

        $this->assertNotNull($timesheet);
        $this->assertSame('submitted', $timesheet->status);
        $this->assertGreaterThan(1.0, (float) $timesheet->hours);
        $this->assertEquals(15.50, (float) $timesheet->hourly_rate);
        $this->assertSame('completed', $shift->fresh()->status);
    }

    public function test_clock_in_rejects_outside_geofence(): void
    {
        Http::fake();

        $client = Client::query()->create([
            'name' => 'Acme',
            'contact_name' => 'Ops',
            'email' => 'ops@acme.test',
            'phone' => '07000000000',
            'address' => '1 Test St',
            'portal_access' => false,
        ]);

        $site = Site::query()->create([
            'client_id' => $client->id,
            'name' => 'Warehouse',
            'address' => '1 Test Street, London',
            'latitude' => 51.5074,
            'longitude' => -0.1278,
            'geofence_radius_meters' => 100,
            'geocoded_at' => now(),
            'is_active' => true,
        ]);

        $guard = Guard::query()->create([
            'full_name' => 'Sam Guard',
            'email' => 'sam.guard@example.com',
            'password' => 'password',
            'must_set_password' => false,
            'sia_licence_number' => '11112222333344',
            'sia_expiry' => now()->addYear(),
            'is_active' => true,
        ]);

        $shift = Shift::query()->create([
            'site_id' => $site->id,
            'guard_id' => $guard->id,
            'starts_at' => now()->subMinutes(5),
            'ends_at' => now()->addHours(4),
            'status' => 'published',
        ]);

        $farLat = 51.52;
        $farLng = -0.14;
        $distance = Geo::distanceMeters(51.5074, -0.1278, $farLat, $farLng);
        $this->assertGreaterThan(100, $distance);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('from the site');

        app(ProcessAttendancePunch::class)($guard, $shift, AttendancePunch::TYPE_CLOCK_IN, [
            'lat' => $farLat,
            'lng' => $farLng,
            'accuracy' => 10,
        ]);
    }

    public function test_hire_sends_staff_invite_mail(): void
    {
        Mail::fake();

        $candidate = Candidate::query()->create([
            'name' => 'Alex Mercer',
            'email' => 'alex.hire@example.com',
            'phone' => '+44 7700 900111',
            'password' => 'password',
        ]);

        $job = JobPosting::query()->create([
            'title' => 'Door Supervisor',
            'slug' => 'door-supervisor-invite',
            'location' => 'London',
            'description' => 'Role',
            'requirements' => ['SIA'],
            'is_active' => true,
        ]);

        $application = Application::query()->create([
            'candidate_id' => $candidate->id,
            'job_posting_id' => $job->id,
            'reference' => 'NX-INVITE-1',
            'sia_licence_number' => '12345678901234',
            'sia_expiry' => now()->addYear()->toDateString(),
            'status' => 'docs_verified',
            'status_history' => [],
        ]);

        $guard = app(HireApplication::class)($application, actorId: 1);

        Mail::assertSent(StaffInviteMail::class, function (StaffInviteMail $mail) use ($guard) {
            return $mail->guard->is($guard);
        });

        $this->assertTrue($guard->must_set_password);
        $this->assertNotNull($guard->invite_token);
    }
}
