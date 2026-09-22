<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Set password · {{ config('norix.name') }}</title>
    <link rel="icon" href="{{ asset('icons/favicon-32.png') }}" type="image/png">
    <meta name="theme-color" content="#050807">
    @vite(['resources/css/app.css'])
</head>
<body class="relative flex min-h-dvh items-center justify-center overflow-hidden px-4">
    <div class="pointer-events-none absolute inset-0 tactical-grid opacity-40"></div>
    <div class="relative w-full max-w-md border border-line bg-surface-2/95 p-6 backdrop-blur">
        <h1 class="font-display text-2xl text-white">Set your password</h1>
        <p class="mt-2 text-sm text-muted">Hi {{ $guard->full_name }} — choose a password for the staff app.</p>
        <form method="POST" action="{{ route('staff.set-password.submit', $token) }}" class="mt-6 grid gap-4">
            @csrf
            <label class="text-sm"><span class="mb-1.5 block font-medium text-ink/90">Password</span><input class="input" type="password" name="password" required autocomplete="new-password"></label>
            <label class="text-sm"><span class="mb-1.5 block font-medium text-ink/90">Confirm password</span><input class="input" type="password" name="password_confirmation" required autocomplete="new-password"></label>
            @error('password')<p class="text-sm text-rose-400">{{ $message }}</p>@enderror
            <button class="btn-primary" type="submit">Save & continue</button>
        </form>
    </div>
</body>
</html>
