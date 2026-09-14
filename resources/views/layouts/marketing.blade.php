<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('norix.name'))</title>
    <meta name="description" content="@yield('meta_description', config('norix.description'))">
    <link rel="icon" href="{{ asset('icons/favicon-32.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta property="og:title" content="@yield('title', config('norix.name'))">
    <meta property="og:description" content="@yield('meta_description', config('norix.description'))">
    <meta property="og:type" content="website">
    <meta name="theme-color" content="#050807">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="flex min-h-screen flex-col">
    <div class="pointer-events-none fixed inset-x-0 top-0 z-50 h-px animate-pulse-line bg-gradient-to-r from-transparent via-accent to-transparent"></div>

    <header class="site-header scan-edge">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-white">
                <img src="{{ asset('images/logo-mark.svg') }}" alt="{{ config('norix.name') }}" class="h-10 w-10 ring-1 ring-accent/30" width="40" height="40">
                <span class="font-display text-xl tracking-tight sm:text-2xl">{{ config('norix.name') }}</span>
            </a>
            <nav class="hidden items-center gap-6 md:flex">
                <a href="{{ route('services') }}" class="nav-link">Services</a>
                <a href="{{ route('about') }}" class="nav-link">About</a>
                <a href="{{ route('careers.index') }}" class="nav-link">Careers</a>
                <a href="{{ route('policies') }}" class="nav-link">Policies</a>
                <a href="{{ route('contact') }}" class="nav-link">Contact</a>
                <a href="{{ route('candidate.login') }}" class="btn-primary !px-4 !py-2 !text-xs">Candidate</a>
            </nav>
            <details class="relative md:hidden">
                <summary class="cursor-pointer list-none text-sm uppercase tracking-wider text-accent">Menu</summary>
                <div class="absolute right-0 mt-3 w-56 border border-line bg-surface-2/95 p-3 text-sm text-white shadow-2xl backdrop-blur">
                    <a class="block py-2 text-white/80 hover:text-accent" href="{{ route('services') }}">Services</a>
                    <a class="block py-2 text-white/80 hover:text-accent" href="{{ route('about') }}">About</a>
                    <a class="block py-2 text-white/80 hover:text-accent" href="{{ route('careers.index') }}">Careers</a>
                    <a class="block py-2 text-white/80 hover:text-accent" href="{{ route('policies') }}">Policies</a>
                    <a class="block py-2 text-white/80 hover:text-accent" href="{{ route('contact') }}">Contact</a>
                    <a class="block py-2 text-accent" href="{{ route('candidate.login') }}">Candidate login</a>
                </div>
            </details>
        </div>
    </header>

    <main class="flex-1">@yield('content')</main>

    <footer class="relative overflow-hidden border-t border-line bg-surface tactical-grid text-white">
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent/60 to-transparent"></div>
        <div class="relative mx-auto grid max-w-6xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-3">
            <div>
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-mark.svg') }}" alt="" class="h-10 w-10" width="40" height="40">
                    <p class="font-display text-2xl">{{ config('norix.name') }}</p>
                </div>
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-muted">{{ config('norix.tagline') }}</p>
                <p class="mt-4 text-xs uppercase tracking-[0.2em] text-signal">SIA-licensed · UK operations</p>
            </div>
            <div>
                <p class="section-kicker">Contact</p>
                <p class="mt-4 text-sm text-ink/90">{{ config('norix.email') }}</p>
                <p class="mt-1 text-sm text-ink/90">{{ config('norix.phone') }}</p>
                <p class="mt-1 text-sm text-muted">{{ config('norix.address') }}</p>
            </div>
            <div>
                <p class="section-kicker">Explore</p>
                <div class="mt-4 flex flex-col gap-2 text-sm text-muted">
                    <a href="{{ route('services') }}" class="hover:text-accent">Services</a>
                    <a href="{{ route('careers.index') }}" class="hover:text-accent">Careers</a>
                    <a href="{{ route('contact') }}" class="hover:text-accent">Request a quote</a>
                    <a href="{{ url('/admin') }}" class="hover:text-accent">Staff portal</a>
                </div>
            </div>
        </div>
        <div class="relative border-t border-line px-4 py-4 text-center text-xs uppercase tracking-wider text-muted/70">
            © {{ date('Y') }} {{ config('norix.legal_name') }}. Controlled access. Continuous vigilance.
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
