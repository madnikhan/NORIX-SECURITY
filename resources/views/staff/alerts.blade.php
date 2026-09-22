@extends('layouts.staff')

@section('title', 'Alerts')
@section('heading', 'Alerts')
@section('subheading', 'Reminders and notices')

@section('content')
    <ul class="grid gap-2">
        @forelse ($notifications as $note)
            <li class="border border-line bg-surface-2/70 px-3 py-3">
                <p class="text-[10px] uppercase tracking-wider text-accent">{{ str_replace('_', ' ', $note->type) }}</p>
                <p class="mt-1 font-medium text-white">{{ $note->title }}</p>
                @if ($note->body)
                    <p class="mt-1 text-sm text-muted">{{ $note->body }}</p>
                @endif
                <p class="mt-2 text-[11px] text-muted/70">{{ $note->created_at->format('j M Y H:i') }}</p>
            </li>
        @empty
            <li class="text-sm text-muted">No alerts yet.</li>
        @endforelse
    </ul>
@endsection
