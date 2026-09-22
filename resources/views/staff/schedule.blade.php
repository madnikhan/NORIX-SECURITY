@extends('layouts.staff')

@section('title', 'Schedule')
@section('heading', 'Shift schedule')
@section('subheading', 'Assigned sites and times')

@section('content')
    <ul class="grid gap-3">
        @forelse ($shifts as $shift)
            @php
                $hasIn = $shift->clockInPunch !== null;
                $hasOut = $shift->clockOutPunch !== null;
                $canClock = $shift->status === 'published'
                    && ! $hasOut
                    && $shift->starts_at->lte(now()->addMinutes((int) config('staff.clock_early_minutes')))
                    && $shift->ends_at->gte(now()->subMinutes((int) config('staff.clock_grace_minutes')));
            @endphp
            <li class="border border-line bg-surface-2/80 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-display text-lg text-white">{{ $shift->site?->name }}</p>
                        <p class="mt-1 text-sm text-muted">{{ $shift->starts_at->format('D j M Y') }}</p>
                        <p class="text-sm text-ink/90">{{ $shift->starts_at->format('H:i') }} – {{ $shift->ends_at->format('H:i') }}</p>
                        <p class="mt-2 text-xs text-muted">{{ $shift->site?->address }}</p>
                        @if ($shift->site?->hasCoordinates())
                            <a class="mt-2 inline-block text-xs text-accent underline" target="_blank" rel="noopener" href="https://www.google.com/maps?q={{ $shift->site->latitude }},{{ $shift->site->longitude }}">Open map</a>
                        @endif
                    </div>
                    <span class="shrink-0 text-[10px] uppercase tracking-wider text-accent">{{ $shift->status }}</span>
                </div>
                <p class="mt-2 text-xs text-muted">
                    @if ($hasOut) Clocked out
                    @elseif ($hasIn) On site
                    @else Not clocked in
                    @endif
                </p>
                @if ($canClock)
                    <form method="POST" action="{{ route('staff.clock', $shift) }}" class="mt-3" data-clock-form>
                        @csrf
                        <input type="hidden" name="type" value="{{ $hasIn ? 'clock_out' : 'clock_in' }}">
                        <input type="hidden" name="lat" value="">
                        <input type="hidden" name="lng" value="">
                        <input type="hidden" name="accuracy" value="">
                        <button type="submit" class="btn-primary w-full">{{ $hasIn ? 'Clock out' : 'Clock in' }}</button>
                        <p class="mt-2 text-center text-xs text-muted" data-clock-status></p>
                    </form>
                @endif
            </li>
        @empty
            <li class="text-sm text-muted">No shifts in your schedule yet.</li>
        @endforelse
    </ul>
@endsection
