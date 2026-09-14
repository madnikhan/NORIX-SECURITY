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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen flex flex-col">
    <header class="absolute inset-x-0 top-0 z-40">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-5 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-white">
                <img src="{{ asset('images/logo-mark.svg') }}" alt="{{ config('norix.name') }}" class="h-9 w-9" width="36" height="36">
                <span class="font-display text-xl tracking-tight sm:text-2xl">{{ config('norix.name') }}</span>
            </a>
            <nav class="hidden items-center gap-7 md:flex">
                <a href="{{ route('services') }}" class="text-sm text-white/75 hover:text-white">Services</a>
                <a href="{{ route('about') }}" class="text-sm text-white/75 hover:text-white">About</a>
                <a href="{{ route('careers.index') }}" class="text-sm text-white/75 hover:text-white">Careers</a>
                <a href="{{ route('policies') }}" class="text-sm text-white/75 hover:text-white">Policies</a>
                <a href="{{ route('contact') }}" class="text-sm text-white/75 hover:text-white">Contact</a>
                <a href="{{ route('candidate.login') }}" class="rounded-sm bg-white px-4 py-2 text-sm font-medium text-ink">Candidate login</a>
            </nav>
            <details class="relative md:hidden">
                <summary class="cursor-pointer list-none text-white">Menu</summary>
                <div class="absolute right-0 mt-3 w-52 rounded-md border border-white/10 bg-ink-deep/95 p-3 text-sm text-white shadow-lg backdrop-blur">
                    <a class="block py-2" href="{{ route('services') }}">Services</a>
                    <a class="block py-2" href="{{ route('about') }}">About</a>
                    <a class="block py-2" href="{{ route('careers.index') }}">Careers</a>
                    <a class="block py-2" href="{{ route('policies') }}">Policies</a>
                    <a class="block py-2" href="{{ route('contact') }}">Contact</a>
                    <a class="block py-2 text-accent" href="{{ route('candidate.login') }}">Candidate login</a>
                </div>
            </details>
        </div>
    </header>

    <main class="flex-1">@yield('content')</main>

    <footer class="border-t border-line bg-ink text-white">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-3">
            <div>
                <p class="font-display text-2xl">{{ config('norix.name') }}</p>
                <p class="mt-3 max-w-sm text-sm text-white/65">{{ config('norix.tagline') }}</p>
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-white/50">Contact</p>
                <p class="mt-3 text-sm text-white/80">{{ config('norix.email') }}</p>
                <p class="mt-1 text-sm text-white/80">{{ config('norix.phone') }}</p>
                <p class="mt-1 text-sm text-white/80">{{ config('norix.address') }}</p>
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-white/50">Explore</p>
                <div class="mt-3 flex flex-col gap-2 text-sm text-white/80">
                    <a href="{{ route('services') }}" class="hover:text-white">Services</a>
                    <a href="{{ route('careers.index') }}" class="hover:text-white">Careers</a>
                    <a href="{{ route('contact') }}" class="hover:text-white">Request a quote</a>
                    <a href="{{ url('/admin') }}" class="hover:text-white">Staff portal</a>
                </div>
            </div>
        </div>
        <div class="border-t border-white/10 px-4 py-4 text-center text-xs text-white/40">
            © {{ date('Y') }} {{ config('norix.legal_name') }}. SIA-licensed security services.
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
