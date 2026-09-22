@extends('layouts.staff')

@section('title', 'Hours')
@section('heading', 'Hours & pay')
@section('subheading', 'Auto timesheets from clock-out')

@section('content')
    <div class="mb-4 grid grid-cols-2 gap-3">
        <div class="border border-line bg-surface-2/80 p-3">
            <p class="text-[11px] uppercase tracking-wider text-muted">Total hours</p>
            <p class="mt-1 font-display text-2xl text-white">{{ number_format($totalHours, 1) }}</p>
        </div>
        <div class="border border-line bg-surface-2/80 p-3">
            <p class="text-[11px] uppercase tracking-wider text-muted">Pay estimate</p>
            <p class="mt-1 font-display text-2xl text-white">£{{ number_format($payEstimate, 2) }}</p>
        </div>
    </div>

    <div class="mb-4 flex justify-end">
        <a href="{{ route('staff.hours.export') }}" class="text-xs uppercase tracking-wider text-accent">Export CSV</a>
    </div>

    <ul class="grid gap-2">
        @forelse ($timesheets as $row)
            <li class="border border-line bg-surface-2/70 px-3 py-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="font-medium text-white">{{ $row->site?->name }}</p>
                        <p class="text-xs text-muted">{{ optional($row->submitted_at)->format('D j M Y H:i') }}</p>
                    </div>
                    <span class="text-[10px] uppercase tracking-wider text-accent">{{ $row->status }}</span>
                </div>
                <p class="mt-2 text-sm text-ink/90">{{ number_format((float) $row->hours, 2) }} hrs · £{{ number_format((float) $row->hourly_rate, 2) }}/hr · £{{ number_format((float) $row->hours * (float) $row->hourly_rate, 2) }}</p>
            </li>
        @empty
            <li class="text-sm text-muted">No timesheets yet. Clock out of a shift to generate one.</li>
        @endforelse
    </ul>

    <div class="mt-4">{{ $timesheets->links() }}</div>
@endsection
