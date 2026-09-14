@extends('layouts.marketing')
@section('title', 'Careers · ' . config('norix.name'))
@section('content')
<section class="hero-surface relative overflow-hidden px-4 pb-16 pt-32 text-white sm:px-6">
    <div class="absolute inset-0 tactical-grid opacity-30"></div>
    <div class="relative mx-auto max-w-6xl">
        <p class="animate-rise section-kicker">Join the roster</p>
        <h1 class="animate-rise mt-3 font-display text-4xl md:text-5xl">Careers</h1>
        <div class="animate-rise-delay mt-4 h-px w-20 bg-accent"></div>
        <p class="animate-rise-delay mt-5 max-w-2xl text-white/75">Join Norix Security. Apply online, upload compliance documents, and track your status.</p>
    </div>
</section>
<section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
    <div class="grid gap-5">
        @forelse($jobs as $job)
            <article class="border border-line bg-surface-2 p-5 md:p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h2 class="font-display text-2xl text-white">{{ $job->title }}</h2>
                        <p class="mt-1 text-sm uppercase tracking-wider text-accent">{{ $job->location }}</p>
                        <p class="mt-3 max-w-2xl text-sm text-muted">{{ $job->description }}</p>
                        @if($job->requirements)
                            <ul class="mt-4 list-disc space-y-1 pl-5 text-sm text-muted">
                                @foreach($job->requirements as $req)
                                    <li>{{ $req }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <a href="{{ route('careers.apply', $job) }}" class="btn-primary shrink-0 !px-4 !py-2">Apply now</a>
                </div>
            </article>
        @empty
            <p class="text-muted">No openings right now. Check back soon.</p>
        @endforelse
    </div>
</section>
@endsection
