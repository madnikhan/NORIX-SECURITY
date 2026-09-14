@php
    $cities = config('norix.cities', []);
@endphp

<section class="coverage-hud relative overflow-hidden border-y border-line bg-surface">
    <div class="pointer-events-none absolute inset-0 tactical-grid opacity-40"></div>
    <div class="pointer-events-none absolute inset-0 hud-vignette"></div>

    <div class="relative mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <div class="reveal flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="section-kicker">Theatre of operations</p>
                <h2 class="mt-3 font-display text-3xl tracking-tight text-white md:text-4xl">Active across England’s major cities</h2>
                <p class="mt-3 max-w-2xl text-muted">SIA-licensed teams ready for CCTV, mobile, retail and manned posts in fifteen priority cities — continuous status, rapid deployment.</p>
            </div>
            <div class="hud-ticker scan-edge border border-line bg-surface-2/80 px-4 py-2 text-xs uppercase tracking-[0.2em] text-accent" aria-live="polite">
                <span class="hud-ticker__label text-signal">Live ·</span>
                <span class="hud-ticker__track" data-city-ticker>
                    @foreach($cities as $city)
                        <span class="hud-ticker__item" data-city-name="{{ $city['name'] }}">{{ $city['name'] }} · ONLINE</span>
                    @endforeach
                </span>
            </div>
        </div>

        <div class="city-scroll reveal mt-10 -mx-4 sm:-mx-6 lg:mx-0">
            <div
                class="city-scroll__track flex gap-3 overflow-x-auto px-4 pb-3 sm:px-6 lg:grid lg:grid-cols-5 lg:overflow-visible lg:px-0 lg:pb-0"
                role="list"
                aria-label="Cities with Norix coverage"
            >
                @foreach($cities as $index => $city)
                    <a
                        href="{{ route('contact', ['city' => $city['name']]) }}"
                        role="listitem"
                        class="city-tile frame-corners group w-[min(72vw,16.5rem)] shrink-0 snap-start border border-line bg-surface-2/90 p-4 transition hover:border-accent/60 sm:w-[min(42vw,15rem)] lg:w-auto lg:shrink"
                        style="--reveal-delay: {{ min($index * 40, 400) }}ms"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <span class="status-dot" aria-hidden="true"></span>
                            <span class="text-[10px] font-semibold uppercase tracking-[0.22em] text-signal">Online</span>
                        </div>
                        <p class="mt-4 font-display text-lg text-white group-hover:text-accent">{{ $city['name'] }}</p>
                        <p class="mt-1 text-xs uppercase tracking-wider text-muted">{{ $city['region'] }}</p>
                    </a>
                @endforeach
            </div>
            <p class="mt-2 px-4 text-xs uppercase tracking-wider text-muted sm:px-6 lg:hidden">Swipe for more cities →</p>
        </div>

        <div class="reveal mt-10 flex flex-wrap items-center gap-4">
            <a href="{{ route('contact') }}" class="btn-primary">Request coverage in your city</a>
            <p class="text-sm text-muted">Select a city tile to prefill your enquiry.</p>
        </div>
    </div>
</section>
