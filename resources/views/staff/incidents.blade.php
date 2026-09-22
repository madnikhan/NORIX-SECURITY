@extends('layouts.staff')

@section('title', 'Incidents')
@section('heading', 'Incidents')
@section('subheading', 'Report issues from site')

@section('content')
    <form method="POST" action="{{ route('staff.incidents.store') }}" enctype="multipart/form-data" class="mb-6 grid gap-3 border border-line bg-surface-2/80 p-4">
        @csrf
        <label class="text-sm">
            <span class="mb-1.5 block text-ink/90">Related shift</span>
            <select name="shift_id" class="input">
                <option value="">Latest / none</option>
                @foreach ($shifts as $shift)
                    <option value="{{ $shift->id }}" @selected(old('shift_id') == $shift->id)>{{ $shift->starts_at->format('j M H:i') }} · {{ $shift->site?->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="text-sm"><span class="mb-1.5 block text-ink/90">Title</span><input class="input" name="title" value="{{ old('title') }}" required></label>
        <label class="text-sm"><span class="mb-1.5 block text-ink/90">Details</span><textarea class="input min-h-28" name="description" required>{{ old('description') }}</textarea></label>
        <label class="text-sm">
            <span class="mb-1.5 block text-ink/90">Severity</span>
            <select name="severity" class="input" required>
                @foreach (['low','medium','high','critical'] as $level)
                    <option value="{{ $level }}" @selected(old('severity', 'medium') === $level)>{{ ucfirst($level) }}</option>
                @endforeach
            </select>
        </label>
        <label class="text-sm"><span class="mb-1.5 block text-ink/90">Photo (optional)</span><input class="input" type="file" name="photo" accept="image/*"></label>
        <button class="btn-primary" type="submit">Submit report</button>
    </form>

    <h2 class="mb-2 font-display text-lg text-white">Your reports</h2>
    <ul class="grid gap-2">
        @forelse ($incidents as $incident)
            <li class="border border-line bg-surface-2/70 px-3 py-3">
                <div class="flex justify-between gap-2">
                    <p class="font-medium text-white">{{ $incident->title }}</p>
                    <span class="text-[10px] uppercase text-accent">{{ $incident->severity }}</span>
                </div>
                <p class="mt-1 text-xs text-muted">{{ $incident->site?->name }} · {{ $incident->created_at->format('j M Y H:i') }}</p>
            </li>
        @empty
            <li class="text-sm text-muted">No incidents reported yet.</li>
        @endforelse
    </ul>
@endsection
