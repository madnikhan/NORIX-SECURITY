<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Staff login · {{ config('norix.name') }}</title>
    <link rel="icon" href="{{ asset('icons/favicon-32.png') }}" type="image/png">
    <link rel="manifest" href="{{ asset('staff.webmanifest') }}">
    <meta name="theme-color" content="#050807">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative flex min-h-dvh items-center justify-center overflow-hidden px-4">
    <div class="pointer-events-none absolute inset-0 tactical-grid opacity-40"></div>
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_20%,rgba(224,179,77,0.12),transparent_50%)]"></div>
    <div class="relative w-full max-w-md border border-line bg-surface-2/95 p-6 backdrop-blur">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo-mark.svg') }}" alt="" class="h-10 w-10 ring-1 ring-accent/30">
            <div>
                <h1 class="font-display text-2xl text-white">{{ config('norix.name') }}</h1>
                <p class="text-sm uppercase tracking-wider text-accent">Staff access</p>
            </div>
        </div>
        <form method="POST" action="{{ route('staff.login.submit') }}" class="mt-6 grid gap-4">
            @csrf
            <label class="text-sm"><span class="mb-1.5 block font-medium text-ink/90">Email</span><input class="input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"></label>
            <label class="text-sm"><span class="mb-1.5 block font-medium text-ink/90">Password</span><input class="input" type="password" name="password" required autocomplete="current-password"></label>
            <label class="flex items-center gap-2 text-sm text-muted"><input type="checkbox" name="remember" value="1"> Remember me</label>
            @error('email')<p class="text-sm text-rose-400">{{ $message }}</p>@enderror
            <button class="btn-primary" type="submit">Sign in</button>
        </form>
        <p class="mt-4 text-xs text-muted">Use the invite email from operations after you are hired.</p>
    </div>
</body>
</html>
