@extends('layouts.marketing')
@section('title', 'Services · ' . config('norix.name'))
@section('content')
<section class="hero-surface px-4 pb-16 pt-28 text-white sm:px-6">
    <div class="mx-auto max-w-6xl">
        <h1 class="animate-rise font-display text-4xl md:text-5xl">Services</h1>
        <p class="animate-rise-delay mt-4 max-w-2xl text-white/75">End-to-end security coverage — monitored, manned, and mobile.</p>
    </div>
</section>
<section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
    <div class="grid gap-12">
        @foreach(config('norix.services') as $index => $service)
            <article id="{{ $service['slug'] }}" class="grid gap-6 border-b border-line pb-12 md:grid-cols-2 md:items-center">
                <figure class="overflow-hidden rounded-sm {{ $index % 2 === 1 ? 'md:order-2' : '' }}">
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
                    <p class="text-sm uppercase tracking-wider text-muted">0{{ $index + 1 }}</p>
                    <h2 class="mt-2 font-display text-2xl md:text-3xl">{{ $service['title'] }}</h2>
                    <p class="mt-3 max-w-xl text-muted">{{ $service['summary'] }} Our teams are SIA-licensed and briefed to your site procedures, escalation paths, and reporting cadence.</p>
                </div>
            </article>
        @endforeach
    </div>
    <div class="mt-12">
        <a href="{{ route('contact') }}" class="btn-ink">Discuss your site</a>
    </div>
</section>
@endsection
