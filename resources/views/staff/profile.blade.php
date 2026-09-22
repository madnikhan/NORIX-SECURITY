@extends('layouts.staff')

@section('title', 'Profile')
@section('heading', 'Profile')
@section('subheading', $guard->email)

@section('content')
    <div class="mb-6 border border-line bg-surface-2/80 p-4">
        <p class="font-display text-xl text-white">{{ $guard->full_name }}</p>
        <p class="mt-2 text-sm text-muted">Phone: {{ $guard->phone ?: '—' }}</p>
        <p class="mt-1 text-sm text-muted">SIA: {{ $guard->sia_licence_number }}</p>
        <p class="mt-1 text-sm {{ $guard->isLicenceExpiringSoon() ? 'text-rose-300' : 'text-muted' }}">Expires: {{ optional($guard->sia_expiry)->format('j M Y') }}</p>
        <p class="mt-1 text-sm text-muted">Rate: £{{ number_format((float) ($guard->default_hourly_rate ?? 0), 2) }}/hr</p>
    </div>

    <form method="POST" action="{{ route('staff.profile.password') }}" class="mb-6 grid gap-3 border border-line bg-surface-2/80 p-4">
        @csrf
        <p class="font-display text-lg text-white">Change password</p>
        <label class="text-sm"><span class="mb-1.5 block">Current</span><input class="input" type="password" name="current_password" required></label>
        <label class="text-sm"><span class="mb-1.5 block">New</span><input class="input" type="password" name="password" required></label>
        <label class="text-sm"><span class="mb-1.5 block">Confirm</span><input class="input" type="password" name="password_confirmation" required></label>
        <button class="btn-primary" type="submit">Update password</button>
    </form>

    <div class="mb-6">
        <h2 class="mb-2 font-display text-lg text-white">Policies</h2>
        <ul class="grid gap-2">
            @foreach ($policies as $policy)
                <li class="flex items-center justify-between gap-3 border border-line bg-surface-2/70 px-3 py-3">
                    <div>
                        <p class="text-sm text-white">{{ $policy->title }}</p>
                        @if (isset($acks[$policy->id]))
                            <p class="text-[11px] text-accent">Acknowledged {{ \Illuminate\Support\Carbon::parse($acks[$policy->id])->format('j M Y') }}</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('staff.policies.acknowledge', $policy) }}">
                        @csrf
                        <button class="text-xs uppercase tracking-wider text-accent" type="submit">{{ isset($acks[$policy->id]) ? 'OK' : 'Ack' }}</button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="grid gap-2">
        <a href="{{ route('staff.analytics') }}" class="border border-line px-3 py-3 text-center text-sm text-white">Analytics</a>
        <a href="{{ route('staff.leave') }}" class="border border-line px-3 py-3 text-center text-sm text-white">Leave requests</a>
        <a href="{{ route('staff.incidents') }}" class="border border-line px-3 py-3 text-center text-sm text-white">Incidents</a>
        <form method="POST" action="{{ route('staff.logout') }}">
            @csrf
            <button class="w-full border border-rose-500/40 px-3 py-3 text-sm text-rose-300" type="submit">Sign out</button>
        </form>
    </div>
@endsection
