@extends('layouts.marketing')

@section('title', config('norix.name') . ' · ' . config('norix.tagline'))

@section('content')
<section class="relative min-h-[100svh] overflow-hidden text-white">
    <div class="absolute inset-0">
        <img
            src="{{ asset('images/hero-security.jpg') }}"
            alt=""
            class="hero-kenburns h-full w-full object-cover {{ file_exists(public_path('videos/hero.mp4')) ? 'opacity-40' : '' }}"
            width="1600"
            height="900"
            fetchpriority="high"
        >
        <div class="absolute inset-0 bg-gradient-to-r from-ink-deep via-ink-deep/85 to-ink-deep/35"></div>
        <div class="absolute inset-0 tactical-grid opacity-40"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_18%_30%,rgba(224,179,77,0.18),transparent_45%)]"></div>
        <div class="hero-scanbeam pointer-events-none absolute inset-0" aria-hidden="true"></div>
        @if(file_exists(public_path('videos/hero.mp4')))
            <video
                class="absolute inset-0 h-full w-full object-cover opacity-45"
                autoplay
                muted
                loop
                playsinline
                poster="{{ asset('images/hero-security.jpg') }}"
            >
                <source src="{{ asset('videos/hero.mp4') }}" type="video/mp4">
            </video>
        @endif
    </div>

    <div class="relative mx-auto flex min-h-[100svh] max-w-6xl flex-col justify-end px-4 pb-20 pt-32 sm:px-6 md:justify-center md:pb-0">
        <p class="animate-rise section-kicker mb-5">Secure · Monitor · Respond</p>
        <img src="{{ asset('images/logo-mark.svg') }}" alt="{{ config('norix.name') }} logo" class="animate-rise mb-5 h-16 w-16 ring-1 ring-accent/40" width="64" height="64">
        <h1 class="animate-rise font-display text-5xl leading-[0.95] tracking-tight sm:text-7xl lg:text-8xl">
            {{ config('norix.name') }}
        </h1>
        <div class="animate-rise-delay mt-4 h-px w-24 bg-accent"></div>
        <p class="animate-rise-delay mt-6 max-w-xl text-base leading-relaxed text-white/75 sm:text-lg">
            {{ config('norix.tagline') }}. Elite SIA-licensed coverage for CCTV, mobile patrol, retail, events and manned posts across England’s major cities.
        </p>
        <div class="animate-rise-delay-2 mt-9 flex flex-wrap gap-3">
            <a href="{{ route('contact') }}" class="btn-primary btn-glow">Request protection</a>
            <a href="{{ route('services') }}" class="btn-ghost">Explore services</a>
        </div>
    </div>
</section>

<section class="relative border-y border-line bg-surface tactical-grid">
    <div class="mx-auto grid max-w-6xl gap-px bg-line sm:grid-cols-3">
        <div class="reveal bg-surface px-6 py-8">
            <p class="section-kicker">Coverage</p>
            <p class="mt-3 font-display text-3xl text-accent">24/7</p>
            <p class="mt-2 text-sm text-muted">Round-the-clock monitoring and response coordination.</p>
        </div>
        <div class="reveal bg-surface px-6 py-8" style="--reveal-delay: 80ms">
            <p class="section-kicker">Compliance</p>
            <p class="mt-3 font-display text-3xl text-accent">SIA</p>
            <p class="mt-2 text-sm text-muted">Licensed officers with verified right-to-work documents.</p>
        </div>
        <div class="reveal bg-surface px-6 py-8" style="--reveal-delay: 160ms">
            <p class="section-kicker">Operations</p>
            <p class="mt-3 font-display text-3xl text-accent">Live</p>
            <p class="mt-2 text-sm text-muted">Site-ready teams for retail, logistics and critical assets.</p>
        </div>
    </div>
</section>

@include('partials.coverage-cities')

<section class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
    <div class="reveal">
        <p class="section-kicker">Capability matrix</p>
        <h2 class="mt-3 font-display text-3xl tracking-tight text-white md:text-4xl">Built for business-critical sites</h2>
        <p class="mt-3 max-w-2xl text-muted">From retail floors to logistics hubs, Norix deploys trained officers and monitoring support tailored to your risk profile.</p>
    </div>
    <div class="mt-12 grid gap-6 sm:grid-cols-2">
        @foreach(array_slice(config('norix.services'), 0, 4) as $index => $service)
            <a href="{{ route('services') }}#{{ $service['slug'] }}" class="group frame-corners reveal block overflow-hidden border border-line bg-surface-2 transition hover:border-accent/50" style="--reveal-delay: {{ $index * 60 }}ms">
                <div class="overflow-hidden">
                    <img
                        src="{{ asset($service['image']) }}"
                        alt="{{ $service['title'] }}"
                        class="aspect-[4/3] w-full object-cover transition duration-700 group-hover:scale-[1.04]"
                        loading="lazy"
                        width="800"
                        height="600"
                    >
                </div>
                <div class="border-t border-line p-5">
                    <h3 class="font-display text-xl text-white">{{ $service['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-muted">{{ $service['summary'] }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>

<section class="relative overflow-hidden border-y border-line bg-surface-2">
    <div class="absolute inset-0 tactical-grid opacity-50"></div>
    <div class="relative mx-auto flex max-w-6xl flex-col gap-6 px-4 py-16 sm:px-6 md:flex-row md:items-center md:justify-between">
        <div class="reveal">
            <p class="section-kicker">Recruitment</p>
            <h2 class="mt-3 font-display text-3xl text-white md:text-4xl">Hiring SIA-licensed officers</h2>
            <p class="mt-3 max-w-xl text-muted">Apply online, upload compliance documents, and track your application status in your candidate dashboard.</p>
        </div>
        <a href="{{ route('careers.index') }}" class="btn-primary btn-glow reveal">View openings</a>
    </div>
</section>

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'SecurityService',
    'name' => config('norix.name'),
    'description' => config('norix.description'),
    'url' => url('/'),
    'telephone' => config('norix.phone'),
    'email' => config('norix.email'),
    'areaServed' => collect(config('norix.cities'))->map(fn ($city) => [
        '@type' => 'City',
        'name' => $city['name'],
        'containedInPlace' => [
            '@type' => 'AdministrativeArea',
            'name' => $city['region'],
        ],
    ])->values()->all(),
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) !!}
</script>
@endpush
@endsection
