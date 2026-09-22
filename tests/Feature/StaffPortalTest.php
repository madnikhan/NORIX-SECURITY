<?php

namespace Tests\Feature;

use App\Models\Guard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_login_and_view_home(): void
    {
        $guard = Guard::query()->create([
            'full_name' => 'Sam Guard',
            'email' => 'sam.staff@example.com',
            'password' => 'password',
            'must_set_password' => false,
            'sia_licence_number' => '11112222333344',
            'sia_expiry' => now()->addYear(),
            'is_active' => true,
        ]);

        $response = $this->post(route('staff.login.submit'), [
            'email' => 'sam.staff@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('staff.home'));
        $this->assertAuthenticatedAs($guard, 'staff');

        $this->get(route('staff.home'))->assertOk()->assertSee('Upcoming', false);
    }

    public function test_inactive_staff_cannot_login(): void
    {
        Guard::query()->create([
            'full_name' => 'Inactive Guard',
            'email' => 'inactive@example.com',
            'password' => 'password',
            'must_set_password' => false,
            'sia_licence_number' => '11112222333344',
            'sia_expiry' => now()->addYear(),
            'is_active' => false,
        ]);

        $this->post(route('staff.login.submit'), [
            'email' => 'inactive@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('staff');
    }

    public function test_invite_token_sets_password_and_logs_in(): void
    {
        $guard = Guard::query()->create([
            'full_name' => 'New Hire',
            'email' => 'new.hire@example.com',
            'must_set_password' => true,
            'sia_licence_number' => '11112222333344',
            'sia_expiry' => now()->addYear(),
            'is_active' => true,
        ]);

        $token = $guard->issueInviteToken();

        $this->get(route('staff.set-password', $token))->assertOk();

        $this->post(route('staff.set-password.submit', $token), [
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertRedirect(route('staff.home'));

        $this->assertAuthenticated('staff');
        $this->assertFalse($guard->fresh()->must_set_password);
        $this->assertNull($guard->fresh()->invite_token);
    }

    public function test_guest_cannot_access_staff_home(): void
    {
        $this->get(route('staff.home'))->assertRedirect(route('staff.login'));
    }
}
