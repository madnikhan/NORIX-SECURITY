@extends('layouts.marketing')
@section('title', 'Contact · ' . config('norix.name'))
@section('content')
<section class="hero-surface px-4 pb-16 pt-28 text-white sm:px-6">
    <div class="mx-auto max-w-6xl">
        <h1 class="animate-rise font-display text-4xl md:text-5xl">Contact</h1>
        <p class="animate-rise-delay mt-4 max-w-2xl text-white/75">Tell us about your sites and coverage needs.</p>
    </div>
</section>
<section class="mx-auto grid max-w-6xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1fr_1.2fr]">
    <div>
        <h2 class="font-display text-2xl">Direct lines</h2>
        <dl class="mt-6 space-y-4 text-sm text-muted">
            <div><dt class="font-medium text-ink">Email</dt><dd>{{ config('norix.email') }}</dd></div>
            <div><dt class="font-medium text-ink">Phone</dt><dd>{{ config('norix.phone') }}</dd></div>
            <div><dt class="font-medium text-ink">Base</dt><dd>{{ config('norix.address') }}</dd></div>
        </dl>
    </div>
    <div class="rounded-lg border border-line bg-white p-5 shadow-sm md:p-6">
        @if(session('success'))
            <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('contact.store') }}" class="grid gap-4">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-sm"><span class="mb-1.5 block font-medium">Company *</span><input class="input" name="company" value="{{ old('company') }}" required></label>
                <label class="text-sm"><span class="mb-1.5 block font-medium">Contact name *</span><input class="input" name="contact_name" value="{{ old('contact_name') }}" required></label>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-sm"><span class="mb-1.5 block font-medium">Email *</span><input class="input" type="email" name="email" value="{{ old('email') }}" required></label>
                <label class="text-sm"><span class="mb-1.5 block font-medium">Phone</span><input class="input" name="phone" value="{{ old('phone') }}"></label>
            </div>
            <label class="text-sm"><span class="mb-1.5 block font-medium">Service interest</span>
                <select class="input" name="service_interest">
                    <option value="">Select a service</option>
                    @foreach(config('norix.services') as $service)
                        <option value="{{ $service['title'] }}" @selected(old('service_interest') === $service['title'])>{{ $service['title'] }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm"><span class="mb-1.5 block font-medium">How can we help? *</span><textarea class="input" name="message" rows="5" required>{{ old('message') }}</textarea></label>
            @if($errors->any())
                <p class="text-sm text-rose-700">{{ $errors->first() }}</p>
            @endif
            <button class="btn-ink" type="submit">Request a quote</button>
        </form>
    </div>
</section>
@endsection
