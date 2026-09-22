<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Staff') · {{ config('norix.name') }}</title>
    <link rel="icon" href="{{ asset('icons/favicon-32.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('staff.webmanifest') }}">
    <meta name="theme-color" content="#050807">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/staff.js'])
    @stack('head')
</head>
<body class="min-h-dvh bg-[#050807] text-ink antialiased">
    <div class="pointer-events-none fixed inset-0 tactical-grid opacity-30"></div>
    <div class="pointer-events-none fixed inset-0 bg-[radial-gradient(ellipse_at_top,rgba(224,179,77,0.08),transparent_55%)]"></div>

    <div class="relative mx-auto flex min-h-dvh max-w-lg flex-col pb-[calc(5.5rem+env(safe-area-inset-bottom))]">
        <header class="sticky top-0 z-30 border-b border-line/80 bg-[#050807]/90 px-4 pb-3 pt-[max(0.75rem,env(safe-area-inset-top))] backdrop-blur">
            <div class="flex items-center justify-between gap-3">
                <div class="flex min-w-0 items-center gap-3">
                    <img src="{{ asset('images/logo-mark.svg') }}" alt="" class="h-9 w-9 shrink-0 ring-1 ring-accent/30">
                    <div class="min-w-0">
                        <p class="truncate font-display text-lg text-white">{{ config('norix.name') }}</p>
                        <p class="text-[11px] uppercase tracking-[0.18em] text-accent">Staff</p>
                    </div>
                </div>
                <a href="{{ route('staff.alerts') }}" class="relative rounded-md border border-line px-3 py-2 text-xs uppercase tracking-wider text-muted hover:text-accent">
                    Alerts
                    @if(($unreadAlerts ?? 0) > 0)
                        <span class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-accent px-1 text-[10px] font-semibold text-black">{{ $unreadAlerts }}</span>
                    @endif
                </a>
            </div>
            <h1 class="mt-3 font-display text-2xl text-white">@yield('heading')</h1>
            @hasSection('subheading')
                <p class="mt-1 text-sm text-muted">@yield('subheading')</p>
            @endif
        </header>

        <main class="flex-1 px-4 py-4">
            @if (session('success'))
                <div class="mb-4 border border-accent/40 bg-accent/10 px-3 py-2 text-sm text-accent">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm text-rose-300">
                    <ul class="list-disc space-y-1 pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-line bg-[#080c0a]/95 pb-[env(safe-area-inset-bottom)] backdrop-blur">
        <div class="mx-auto grid max-w-lg grid-cols-5 gap-1 px-1 py-2 text-center text-[10px] uppercase tracking-wider text-muted">
            <a href="{{ route('staff.home') }}" class="{{ request()->routeIs('staff.home') ? 'text-accent' : '' }} flex flex-col items-center gap-1 rounded-md px-1 py-2 hover:text-white">
                <span class="text-base leading-none">⌂</span>Home
            </a>
            <a href="{{ route('staff.schedule') }}" class="{{ request()->routeIs('staff.schedule') ? 'text-accent' : '' }} flex flex-col items-center gap-1 rounded-md px-1 py-2 hover:text-white">
                <span class="text-base leading-none">▦</span>Shifts
            </a>
            <a href="{{ route('staff.hours') }}" class="{{ request()->routeIs('staff.hours') ? 'text-accent' : '' }} flex flex-col items-center gap-1 rounded-md px-1 py-2 hover:text-white">
                <span class="text-base leading-none">◷</span>Hours
            </a>
            <a href="{{ route('staff.messages') }}" class="{{ request()->routeIs('staff.messages') ? 'text-accent' : '' }} flex flex-col items-center gap-1 rounded-md px-1 py-2 hover:text-white">
                <span class="text-base leading-none">✉</span>Inbox
            </a>
            <a href="{{ route('staff.profile') }}" class="{{ request()->routeIs('staff.profile') ? 'text-accent' : '' }} flex flex-col items-center gap-1 rounded-md px-1 py-2 hover:text-white">
                <span class="text-base leading-none">◎</span>Me
            </a>
        </div>
    </nav>

    @stack('scripts')
</body>
</html>
