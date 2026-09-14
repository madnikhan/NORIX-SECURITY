<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Candidate login · {{ config('norix.name') }}</title>
    <link rel="icon" href="{{ asset('icons/favicon-32.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-panel px-4">
    <div class="w-full max-w-md rounded-lg border border-line bg-white p-6 shadow-sm">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo-mark.svg') }}" alt="" class="h-10 w-10">
            <div>
                <h1 class="font-display text-2xl">{{ config('norix.name') }}</h1>
                <p class="text-sm text-muted">Candidate dashboard login</p>
            </div>
        </div>
        <form method="POST" action="{{ route('candidate.login.submit') }}" class="mt-6 grid gap-4">
            @csrf
            <label class="text-sm"><span class="mb-1.5 block font-medium">Email</span><input class="input" type="email" name="email" value="{{ old('email') }}" required></label>
            <label class="text-sm"><span class="mb-1.5 block font-medium">Password</span><input class="input" type="password" name="password" required></label>
            @error('email')<p class="text-sm text-rose-700">{{ $message }}</p>@enderror
            <button class="btn-ink" type="submit">Sign in</button>
        </form>
        <p class="mt-4 text-xs text-muted">Created when you apply for a role. <a class="underline" href="{{ route('careers.index') }}">View openings</a></p>
    </div>
</body>
</html>
