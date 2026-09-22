@extends('layouts.staff')

@section('title', 'Leave')
@section('heading', 'Leave')
@section('subheading', 'Request time off')

@section('content')
    <form method="POST" action="{{ route('staff.leave.store') }}" class="mb-6 grid gap-3 border border-line bg-surface-2/80 p-4">
        @csrf
        <label class="text-sm"><span class="mb-1.5 block text-ink/90">From</span><input class="input" type="date" name="starts_on" value="{{ old('starts_on') }}" required></label>
        <label class="text-sm"><span class="mb-1.5 block text-ink/90">To</span><input class="input" type="date" name="ends_on" value="{{ old('ends_on') }}" required></label>
        <label class="text-sm"><span class="mb-1.5 block text-ink/90">Reason</span><textarea class="input min-h-24" name="reason">{{ old('reason') }}</textarea></label>
        <button class="btn-primary" type="submit">Submit request</button>
    </form>

    <ul class="grid gap-2">
        @forelse ($requests as $request)
            <li class="border border-line bg-surface-2/70 px-3 py-3">
                <div class="flex justify-between gap-2">
                    <p class="text-white">{{ $request->starts_on->format('j M Y') }} – {{ $request->ends_on->format('j M Y') }}</p>
                    <span class="text-[10px] uppercase text-accent">{{ $request->status }}</span>
                </div>
                @if ($request->reason)
                    <p class="mt-1 text-sm text-muted">{{ $request->reason }}</p>
                @endif
            </li>
        @empty
            <li class="text-sm text-muted">No leave requests yet.</li>
        @endforelse
    </ul>
@endsection
