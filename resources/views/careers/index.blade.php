@extends('layouts.marketing')
@section('title', 'Careers · ' . config('norix.name'))
@section('content')
<section class="hero-surface px-4 pb-16 pt-28 text-white sm:px-6">
    <div class="mx-auto max-w-6xl">
        <h1 class="animate-rise font-display text-4xl md:text-5xl">Careers</h1>
        <p class="animate-rise-delay mt-4 max-w-2xl text-white/75">Join Norix Security. Apply online, upload compliance documents, and track your status.</p>
    </div>
</section>
<section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
    <div class="grid gap-6">
        @forelse($jobs as $job)
            <article class="rounded-lg border border-line bg-white p-5 shadow-sm md:p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h2 class="font-display text-2xl">{{ $job->title }}</h2>
                        <p class="mt-1 text-sm text-muted">{{ $job->location }}</p>
                        <p class="mt-3 max-w-2xl text-sm text-muted">{{ $job->description }}</p>
                        @if($job->requirements)
                            <ul class="mt-4 list-disc space-y-1 pl-5 text-sm text-muted">
                                @foreach($job->requirements as $req)
                                    <li>{{ $req }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <a href="{{ route('careers.apply', $job) }}" class="btn-ink shrink-0 px-4 py-2">Apply now</a>
                </div>
            </article>
        @empty
            <p class="text-muted">No openings right now. Check back soon.</p>
        @endforelse
    </div>
</section>
@endsection
