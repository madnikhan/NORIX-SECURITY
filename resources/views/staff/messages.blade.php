@extends('layouts.staff')

@section('title', 'Messages')
@section('heading', 'Messages')
@section('subheading', 'Chat with operations')

@section('content')
    <div class="mb-4 max-h-[50vh] space-y-3 overflow-y-auto border border-line bg-surface-2/60 p-3">
        @forelse ($messages as $message)
            @php $mine = $message->sender_type === \App\Models\Guard::class; @endphp
            <div class="{{ $mine ? 'ml-8 border-accent/30' : 'mr-8 border-line' }} border bg-[#0a100e] px-3 py-2">
                <p class="text-[10px] uppercase tracking-wider text-muted">{{ $mine ? 'You' : 'Operations' }} · {{ $message->created_at->format('j M H:i') }}</p>
                <p class="mt-1 whitespace-pre-wrap text-sm text-ink/90">{{ $message->body }}</p>
            </div>
        @empty
            <p class="text-sm text-muted">No messages yet. Say hello to ops.</p>
        @endforelse
    </div>

    <form method="POST" action="{{ route('staff.messages.store') }}" class="grid gap-3">
        @csrf
        <textarea class="input min-h-24" name="body" placeholder="Write a message…" required>{{ old('body') }}</textarea>
        <button class="btn-primary" type="submit">Send</button>
    </form>
@endsection
