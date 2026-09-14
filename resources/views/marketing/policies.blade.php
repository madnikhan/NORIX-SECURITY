@extends('layouts.marketing')
@section('title', 'Policies · ' . config('norix.name'))
@section('content')
<section class="hero-surface relative overflow-hidden px-4 pb-16 pt-32 text-white sm:px-6">
    <div class="absolute inset-0 tactical-grid opacity-30"></div>
    <div class="relative mx-auto max-w-6xl">
        <p class="animate-rise section-kicker">Governance</p>
        <h1 class="animate-rise mt-3 font-display text-4xl md:text-5xl">Policies</h1>
        <div class="animate-rise-delay mt-4 h-px w-20 bg-accent"></div>
        <p class="animate-rise-delay mt-5 max-w-2xl text-white/75">Corporate standards that guide how we operate every contract.</p>
    </div>
</section>
<section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
    <div class="grid gap-3">
        @forelse($policies as $policy)
            <div class="flex flex-col gap-3 border border-line bg-surface-2 p-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="font-medium text-white">{{ $policy->title }}</h2>
                @if($policy->file_path)
                    <a href="{{ route('policies.download', $policy) }}" class="btn-ink !px-4 !py-2 !text-xs">Download</a>
                @else
                    <span class="text-sm uppercase tracking-wider text-muted">Available on request</span>
                @endif
            </div>
        @empty
            <p class="text-muted">Policy documents will appear here once published.</p>
        @endforelse
    </div>
</section>
@endsection
