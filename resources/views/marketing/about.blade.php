@extends('layouts.marketing')
@section('title', 'About · ' . config('norix.name'))
@section('content')
<section class="hero-surface relative overflow-hidden px-4 pb-16 pt-32 text-white sm:px-6">
    <div class="absolute inset-0 tactical-grid opacity-30"></div>
    <div class="relative mx-auto max-w-6xl">
        <p class="animate-rise section-kicker">Who we are</p>
        <h1 class="animate-rise mt-3 font-display text-4xl md:text-5xl">About {{ config('norix.name') }}</h1>
        <div class="animate-rise-delay mt-4 h-px w-20 bg-accent"></div>
        <p class="animate-rise-delay mt-5 max-w-2xl text-white/75">Your safety is our commitment — delivered through trained, SIA-licensed teams and clear operational reporting.</p>
    </div>
</section>
<section class="mx-auto grid max-w-6xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
    <div class="max-w-3xl space-y-6 text-muted">
        <p class="text-ink/90">{{ config('norix.name') }} works closely with commercial clients to create secure, welcoming environments. Quality is delivered through a trained team who treat your site as an extension of your brand.</p>
        <p>Instruction, preparation, and professionalism are the foundation of our service. Communication flows through central coordination so response is fast when risk appears — without disrupting day-to-day operations.</p>
        <p>Recruitment includes document verification for passport, SIA licence, and right-to-work evidence before deployment.</p>
    </div>
    <figure class="frame-corners overflow-hidden border border-line bg-surface-2">
        <img
            src="{{ asset('images/about-team.jpg') }}"
            alt="Norix Security officer on duty"
            class="aspect-[4/3] w-full object-cover"
            loading="lazy"
            width="800"
            height="600"
        >
    </figure>
</section>
@endsection
