@extends('layouts.marketing')
@section('title', 'Contact · ' . config('norix.name'))
@section('content')
@php
    $selectedCity = old('city', request('city'));
@endphp
<section class="hero-surface relative overflow-hidden px-4 pb-16 pt-32 text-white sm:px-6">
    <div class="absolute inset-0 tactical-grid opacity-30"></div>
    <div class="hero-scanbeam pointer-events-none absolute inset-0 opacity-50" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-6xl">
        <p class="animate-rise section-kicker">Engagement</p>
        <h1 class="animate-rise mt-3 font-display text-4xl md:text-5xl">Contact</h1>
        <div class="animate-rise-delay mt-4 h-px w-20 bg-accent"></div>
        <p class="animate-rise-delay mt-5 max-w-2xl text-white/75">Tell us about your sites and coverage needs across England’s major cities.</p>
    </div>
</section>
<section class="mx-auto grid max-w-6xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1fr_1.2fr]">
    <div class="reveal">
        <p class="section-kicker">Direct lines</p>
        <h2 class="mt-3 font-display text-2xl text-white">Command channel</h2>
        <dl class="mt-6 space-y-4 text-sm text-muted">
            <div><dt class="font-medium text-accent">Email</dt><dd class="mt-1 text-ink/90">{{ config('norix.email') }}</dd></div>
            <div><dt class="font-medium text-accent">Phone</dt><dd class="mt-1 text-ink/90">{{ config('norix.phone') }}</dd></div>
            <div><dt class="font-medium text-accent">Base</dt><dd class="mt-1 text-ink/90">{{ config('norix.address') }}</dd></div>
        </dl>
    </div>
    <div class="reveal border border-line bg-surface-2 p-5 md:p-6" style="--reveal-delay: 100ms">
        @if(session('success'))
            <div class="mb-4 border border-signal/30 bg-signal/10 p-4 text-sm text-signal">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('contact.store') }}" class="grid gap-4">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-sm"><span class="mb-1.5 block font-medium text-ink/90">Company *</span><input class="input" name="company" value="{{ old('company') }}" required></label>
                <label class="text-sm"><span class="mb-1.5 block font-medium text-ink/90">Contact name *</span><input class="input" name="contact_name" value="{{ old('contact_name') }}" required></label>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-sm"><span class="mb-1.5 block font-medium text-ink/90">Email *</span><input class="input" type="email" name="email" value="{{ old('email') }}" required></label>
                <label class="text-sm"><span class="mb-1.5 block font-medium text-ink/90">Phone</span><input class="input" name="phone" value="{{ old('phone') }}"></label>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-sm"><span class="mb-1.5 block font-medium text-ink/90">City *</span>
                    <select class="input" name="city" required>
                        <option value="">Select a city</option>
                        @foreach(config('norix.cities') as $city)
                            <option value="{{ $city['name'] }}" @selected($selectedCity === $city['name'])>{{ $city['name'] }}</option>
                        @endforeach
                        <option value="Other" @selected($selectedCity === 'Other')>Other / multiple sites</option>
                    </select>
                </label>
                <label class="text-sm"><span class="mb-1.5 block font-medium text-ink/90">Service interest</span>
                    <select class="input" name="service_interest">
                        <option value="">Select a service</option>
                        @foreach(config('norix.services') as $service)
                            <option value="{{ $service['title'] }}" @selected(old('service_interest') === $service['title'])>{{ $service['title'] }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <label class="text-sm"><span class="mb-1.5 block font-medium text-ink/90">How can we help? *</span><textarea class="input" name="message" rows="5" required>{{ old('message') }}</textarea></label>
            @if($errors->any())
                <p class="text-sm text-rose-400">{{ $errors->first() }}</p>
            @endif
            <button class="btn-primary btn-glow" type="submit">Request a quote</button>
        </form>
    </div>
</section>
@endsection
