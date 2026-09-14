@extends('layouts.marketing')

@section('title', config('norix.name') . ' · ' . config('norix.tagline'))

@section('content')
<section class="relative min-h-[100svh] overflow-hidden text-white">
    <div class="absolute inset-0">
        <img
            src="{{ asset('images/hero-security.jpg') }}"
            alt=""
            class="h-full w-full object-cover"
            width="1600"
            height="900"
            fetchpriority="high"
        >
        <div class="absolute inset-0 bg-gradient-to-r from-ink-deep/90 via-ink-deep/70 to-ink-deep/40"></div>
        <div class="absolute inset-0 animate-fade bg-[radial-gradient(circle_at_20%_20%,rgba(196,163,90,0.22),transparent_40%)]"></div>
        @if(file_exists(public_path('videos/hero.mp4')))
            <video class="absolute inset-0 h-full w-full object-cover opacity-30" autoplay muted loop playsinline poster="{{ asset('images/hero-security.jpg') }}">
                <source src="{{ asset('videos/hero.mp4') }}" type="video/mp4">
            </video>
        @endif
    </div>
    <div class="relative mx-auto flex min-h-[100svh] max-w-6xl flex-col justify-end px-4 pb-20 pt-28 sm:px-6 md:justify-center md:pb-0">
        <img src="{{ asset('images/logo-mark.svg') }}" alt="{{ config('norix.name') }} logo" class="animate-rise mb-6 h-14 w-14" width="56" height="56">
        <h1 class="animate-rise font-display text-5xl leading-none tracking-tight sm:text-7xl lg:text-8xl">
            {{ config('norix.name') }}
        </h1>
        <p class="animate-rise-delay mt-5 max-w-xl text-base text-white/75 sm:text-lg">
            {{ config('norix.tagline') }}. SIA-licensed CCTV, mobile, retail, events and manned guarding for B2B sites across the UK.
        </p>
        <div class="animate-rise-delay-2 mt-8 flex flex-wrap gap-3">
            <a href="{{ route('contact') }}" class="btn-primary">Request a quote</a>
            <a href="{{ route('services') }}" class="btn-ghost">View services</a>
        </div>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
    <h2 class="font-display text-3xl tracking-tight md:text-[2.125rem]">Built for business-critical sites</h2>
    <p class="mt-3 max-w-2xl text-muted">From retail floors to logistics hubs, Norix deploys trained officers and monitoring support tailored to your risk profile.</p>
    <div class="mt-10 grid gap-8 sm:grid-cols-2">
        @foreach(array_slice(config('norix.services'), 0, 4) as $service)
            <a href="{{ route('services') }}#{{ $service['slug'] }}" class="group block overflow-hidden border-t border-line pt-6">
                <div class="overflow-hidden rounded-sm">
                    <img
                        src="{{ asset($service['image']) }}"
                        alt="{{ $service['title'] }}"
                        class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                        loading="lazy"
                        width="800"
                        height="600"
                    >
                </div>
                <h3 class="mt-4 font-display text-xl">{{ $service['title'] }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-muted">{{ $service['summary'] }}</p>
            </a>
        @endforeach
    </div>
</section>

<section class="border-y border-line bg-white">
    <div class="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-16 sm:px-6 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="font-display text-3xl">Hiring SIA-licensed officers</h2>
            <p class="mt-2 max-w-xl text-muted">Apply online, upload compliance documents, and track your application status in your candidate dashboard.</p>
        </div>
        <a href="{{ route('careers.index') }}" class="btn-ink">View openings</a>
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
    'areaServed' => 'GB',
], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}
</script>
@endpush
@endsection
