@extends('layouts.marketing')
@section('title', 'Policies · ' . config('norix.name'))
@section('content')
<section class="hero-surface px-4 pb-16 pt-28 text-white sm:px-6">
    <div class="mx-auto max-w-6xl">
        <h1 class="animate-rise font-display text-4xl md:text-5xl">Policies</h1>
        <p class="animate-rise-delay mt-4 max-w-2xl text-white/75">Corporate standards that guide how we operate every contract.</p>
    </div>
</section>
<section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
    <div class="grid gap-4">
        @forelse($policies as $policy)
            <div class="flex flex-col gap-3 rounded-lg border border-line bg-white p-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="font-medium">{{ $policy->title }}</h2>
                @if($policy->file_path)
                    <a href="{{ route('policies.download', $policy) }}" class="btn-ink px-4 py-2 text-xs">Download</a>
                @else
                    <span class="text-sm text-muted">Available on request</span>
                @endif
            </div>
        @empty
            <p class="text-muted">Policy documents will appear here once published.</p>
        @endforelse
    </div>
</section>
@endsection
