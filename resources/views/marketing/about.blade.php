@extends('layouts.marketing')
@section('title', 'About · ' . config('norix.name'))
@section('content')
<section class="hero-surface px-4 pb-16 pt-28 text-white sm:px-6">
    <div class="mx-auto max-w-6xl">
        <h1 class="animate-rise font-display text-4xl md:text-5xl">About {{ config('norix.name') }}</h1>
        <p class="animate-rise-delay mt-4 max-w-2xl text-white/75">Your safety is our commitment — delivered through trained, SIA-licensed teams and clear operational reporting.</p>
    </div>
</section>
<section class="mx-auto grid max-w-6xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:items-start">
    <div class="max-w-3xl space-y-6 text-muted">
        <p>{{ config('norix.name') }} works closely with commercial clients to create secure, welcoming environments. Quality is delivered through a trained team who treat your site as an extension of your brand.</p>
        <p>Instruction, preparation, and professionalism are the foundation of our service. Communication flows through central coordination so response is fast when risk appears — without disrupting day-to-day operations.</p>
        <p>Recruitment includes document verification for passport, SIA licence, and right-to-work evidence before deployment.</p>
    </div>
    <figure class="overflow-hidden rounded-sm">
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
