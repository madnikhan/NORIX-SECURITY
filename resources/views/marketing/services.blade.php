@extends('layouts.marketing')
@section('title', 'Services · ' . config('norix.name'))
@section('content')
<section class="hero-surface relative overflow-hidden px-4 pb-16 pt-32 text-white sm:px-6">
    <div class="absolute inset-0 tactical-grid opacity-30"></div>
    <div class="hero-scanbeam pointer-events-none absolute inset-0 opacity-60" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-6xl">
        <p class="animate-rise section-kicker">Operations catalogue</p>
        <h1 class="animate-rise mt-3 font-display text-4xl md:text-5xl">Services</h1>
        <div class="animate-rise-delay mt-4 h-px w-20 bg-accent"></div>
        <p class="animate-rise-delay mt-5 max-w-2xl text-white/75">End-to-end security coverage across England’s major cities — monitored, manned, and mobile.</p>
    </div>
</section>
<section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
    <div class="grid gap-14">
        @foreach(config('norix.services') as $index => $service)
            <article id="{{ $service['slug'] }}" class="reveal grid gap-6 border-b border-line pb-14 md:grid-cols-2 md:items-center" style="--reveal-delay: {{ min($index * 50, 200) }}ms">
                <figure class="frame-corners overflow-hidden border border-line {{ $index % 2 === 1 ? 'md:order-2' : '' }}">
                    <img
                        src="{{ asset($service['image']) }}"
                        alt="{{ $service['title'] }}"
                        class="aspect-[4/3] w-full object-cover"
                        loading="lazy"
                        width="800"
                        height="600"
                    >
                </figure>
                <div class="{{ $index % 2 === 1 ? 'md:order-1' : '' }}">
                    <p class="section-kicker">Unit 0{{ $index + 1 }}</p>
                    <h2 class="mt-3 font-display text-2xl text-white md:text-3xl">{{ $service['title'] }}</h2>
                    <p class="mt-4 max-w-xl text-muted">{{ $service['summary'] }} Our teams are SIA-licensed and briefed to your site procedures, escalation paths, and reporting cadence.</p>
                </div>
            </article>
        @endforeach
    </div>
    <div class="mt-12">
        <a href="{{ route('contact') }}" class="btn-primary btn-glow">Discuss your site</a>
    </div>
</section>

@include('partials.coverage-cities')
@endsection
