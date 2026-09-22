<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class StaffAuthController extends Controller
{
    public function showLogin()
    {
        return view('staff.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('staff')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
        }

        /** @var Guard $guard */
        $guard = Auth::guard('staff')->user();

        if (! $guard->is_active) {
            Auth::guard('staff')->logout();

            return back()->withErrors(['email' => 'Your staff account is inactive.'])->onlyInput('email');
        }

        if ($guard->must_set_password || blank($guard->password)) {
            Auth::guard('staff')->logout();

            return back()->withErrors(['email' => 'Please use your invite link to set a password first.'])->onlyInput('email');
        }

        $guard->forceFill(['last_login_at' => now()])->save();
        $request->session()->regenerate();

        return redirect()->intended(route('staff.home'));
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login');
    }

    public function showSetPassword(string $token)
    {
        $guard = Guard::findByInviteToken($token);

        if ($guard === null) {
            return redirect()->route('staff.login')
                ->withErrors(['email' => 'This invite link is invalid or has already been used.']);
        }

        return view('staff.set-password', [
            'token' => $token,
            'guard' => $guard,
        ]);
    }

    public function setPassword(Request $request, string $token)
    {
        $guard = Guard::findByInviteToken($token);

        if ($guard === null) {
            return redirect()->route('staff.login')
                ->withErrors(['email' => 'This invite link is invalid or has already been used.']);
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $guard->forceFill([
            'password' => $validated['password'],
            'must_set_password' => false,
            'invite_token' => null,
            'last_login_at' => now(),
        ])->save();

        Auth::guard('staff')->login($guard);
        $request->session()->regenerate();

        return redirect()->route('staff.home')
            ->with('success', 'Password set. Welcome to the staff app.');
    }
}
