@extends('layouts.staff')

@section('title', 'Analytics')
@section('heading', 'Analytics')
@section('subheading', 'Hours and punctuality')

@section('content')
    <div class="grid gap-3">
        <div class="grid grid-cols-2 gap-3">
            <div class="border border-line bg-surface-2/80 p-3">
                <p class="text-[11px] uppercase tracking-wider text-muted">This week</p>
                <p class="mt-1 font-display text-2xl text-white">{{ number_format($weekHours, 1) }}h</p>
            </div>
            <div class="border border-line bg-surface-2/80 p-3">
                <p class="text-[11px] uppercase tracking-wider text-muted">This month</p>
                <p class="mt-1 font-display text-2xl text-white">{{ number_format($monthHours, 1) }}h</p>
            </div>
        </div>
        <div class="border border-line bg-surface-2/80 p-4">
            <p class="text-[11px] uppercase tracking-wider text-accent">On-time rate</p>
            <p class="mt-2 font-display text-4xl text-white">{{ $onTimePct }}%</p>
            <p class="mt-2 text-sm text-muted">{{ $onTime }} on time · {{ $late }} late · {{ $early }} early</p>
        </div>
        <div class="border border-line bg-surface-2/80 p-4">
            <p class="text-[11px] uppercase tracking-wider text-muted">Sites worked (90d timesheets)</p>
            <p class="mt-1 font-display text-2xl text-white">{{ $sitesWorked }}</p>
        </div>
    </div>
@endsection
