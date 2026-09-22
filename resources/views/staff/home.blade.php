@extends('layouts.staff')

@section('title', 'Home')
@section('heading', 'Hi, '.explode(' ', trim($guard->full_name))[0])
@section('subheading', 'Your next shift and clock controls')

@section('content')
    <div class="grid gap-4">
        <div class="border border-line bg-surface-2/80 p-4">
            <p class="text-[11px] uppercase tracking-[0.18em] text-accent">This week</p>
            <p class="mt-2 font-display text-3xl text-white">{{ number_format($weekHours, 1) }} <span class="text-lg text-muted">hrs</span></p>
        </div>

        @if ($activeShift)
            @php
                $hasIn = $activeShift->clockInPunch !== null;
                $hasOut = $activeShift->clockOutPunch !== null;
            @endphp
            <div class="border border-accent/30 bg-surface-2/90 p-4">
                <p class="text-[11px] uppercase tracking-[0.18em] text-accent">Active shift</p>
                <h2 class="mt-2 font-display text-xl text-white">{{ $activeShift->site?->name }}</h2>
                <p class="mt-1 text-sm text-muted">{{ $activeShift->starts_at->timezone(config('app.timezone'))->format('D j M · H:i') }} – {{ $activeShift->ends_at->format('H:i') }}</p>
                <p class="mt-2 text-sm text-ink/80">{{ $activeShift->site?->address }}</p>

                @if (! $hasOut)
                    <form method="POST" action="{{ route('staff.clock', $activeShift) }}" class="mt-4" data-clock-form>
                        @csrf
                        <input type="hidden" name="type" value="{{ $hasIn ? 'clock_out' : 'clock_in' }}">
                        <input type="hidden" name="lat" value="">
                        <input type="hidden" name="lng" value="">
                        <input type="hidden" name="accuracy" value="">
                        <button type="submit" class="btn-primary w-full !py-3 text-base">
                            {{ $hasIn ? 'Clock out' : 'Clock in' }}
                        </button>
                        <p class="mt-2 text-center text-xs text-muted" data-clock-status></p>
                    </form>
                @else
                    <p class="mt-4 text-sm text-accent">Shift completed — timesheet submitted.</p>
                @endif
            </div>
        @else
            <div class="border border-line bg-surface-2/80 p-4 text-sm text-muted">No shift open for clocking right now.</div>
        @endif

        <div>
            <div class="mb-2 flex items-center justify-between">
                <h2 class="font-display text-lg text-white">Upcoming</h2>
                <a href="{{ route('staff.schedule') }}" class="text-xs uppercase tracking-wider text-accent">All</a>
            </div>
            <ul class="grid gap-2">
                @forelse ($upcoming as $shift)
                    <li class="border border-line bg-surface-2/70 px-3 py-3">
                        <p class="font-medium text-white">{{ $shift->site?->name }}</p>
                        <p class="text-xs text-muted">{{ $shift->starts_at->format('D j M H:i') }} – {{ $shift->ends_at->format('H:i') }}</p>
                    </li>
                @empty
                    <li class="text-sm text-muted">No upcoming shifts assigned.</li>
                @endforelse
            </ul>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('staff.analytics') }}" class="border border-line bg-surface-2/70 px-3 py-3 text-center text-sm text-white hover:border-accent/50">Analytics</a>
            <a href="{{ route('staff.incidents') }}" class="border border-line bg-surface-2/70 px-3 py-3 text-center text-sm text-white hover:border-accent/50">Report incident</a>
            <a href="{{ route('staff.leave') }}" class="border border-line bg-surface-2/70 px-3 py-3 text-center text-sm text-white hover:border-accent/50">Leave</a>
            <a href="{{ route('staff.alerts') }}" class="border border-line bg-surface-2/70 px-3 py-3 text-center text-sm text-white hover:border-accent/50">Alerts</a>
        </div>
    </div>
@endsection
