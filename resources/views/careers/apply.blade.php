@extends('layouts.marketing')
@section('title', 'Apply · ' . $job->title)
@section('content')
<section class="hero-surface px-4 pb-12 pt-28 text-white sm:px-6">
    <div class="mx-auto max-w-3xl">
        <p class="text-sm text-white/60">Applying for</p>
        <h1 class="mt-2 font-display text-3xl md:text-4xl">{{ $job->title }}</h1>
        <p class="mt-2 text-white/70">{{ $job->location }}</p>
    </div>
</section>
<section class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <form method="POST" action="{{ route('careers.store', $job) }}" enctype="multipart/form-data" class="rounded-lg border border-line bg-white p-5 shadow-sm md:p-6">
        @csrf
        <div class="grid gap-4">
            <label class="text-sm"><span class="mb-1.5 block font-medium">Full name *</span><input class="input" name="full_name" value="{{ old('full_name') }}" required></label>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-sm"><span class="mb-1.5 block font-medium">Email *</span><input class="input" type="email" name="email" value="{{ old('email') }}" required></label>
                <label class="text-sm"><span class="mb-1.5 block font-medium">Phone *</span><input class="input" name="phone" value="{{ old('phone') }}" required></label>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-sm"><span class="mb-1.5 block font-medium">Dashboard password *</span><input class="input" type="password" name="password" required></label>
                <label class="text-sm"><span class="mb-1.5 block font-medium">Confirm password *</span><input class="input" type="password" name="password_confirmation" required></label>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-sm"><span class="mb-1.5 block font-medium">SIA licence number</span><input class="input" name="sia_licence_number" value="{{ old('sia_licence_number') }}"></label>
                <label class="text-sm"><span class="mb-1.5 block font-medium">SIA expiry</span><input class="input" type="date" name="sia_expiry" value="{{ old('sia_expiry') }}"></label>
            </div>
            <label class="text-sm"><span class="mb-1.5 block font-medium">Right to work share code</span><input class="input" name="right_to_work_share_code" value="{{ old('right_to_work_share_code') }}"></label>
            <hr class="border-line">
            <p class="text-sm text-muted">Upload clear scans or photos (PDF/JPG/PNG, max 10MB each).</p>
            <label class="text-sm"><span class="mb-1.5 block font-medium">Passport *</span><input class="input" type="file" name="passport" accept=".pdf,image/*" required></label>
            <label class="text-sm"><span class="mb-1.5 block font-medium">SIA licence *</span><input class="input" type="file" name="sia_licence" accept=".pdf,image/*" required></label>
            <label class="text-sm"><span class="mb-1.5 block font-medium">Right to work share code evidence *</span><input class="input" type="file" name="right_to_work_share_code_file" accept=".pdf,image/*" required></label>
            <label class="text-sm"><span class="mb-1.5 block font-medium">BRP (optional)</span><input class="input" type="file" name="brp" accept=".pdf,image/*"></label>
            <label class="text-sm"><span class="mb-1.5 block font-medium">Work permit (optional)</span><input class="input" type="file" name="work_permit" accept=".pdf,image/*"></label>
            <label class="text-sm"><span class="mb-1.5 block font-medium">Other (optional)</span><input class="input" type="file" name="other" accept=".pdf,image/*"></label>
            @if($errors->any())
                <div class="rounded-md bg-rose-50 p-3 text-sm text-rose-800">
                    <ul class="list-disc pl-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <button type="submit" class="btn-ink">Submit application</button>
        </div>
    </form>
</section>
@endsection
