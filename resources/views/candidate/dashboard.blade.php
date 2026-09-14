<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My applications · {{ config('norix.name') }}</title>
    <link rel="icon" href="{{ asset('icons/favicon-32.png') }}" type="image/png">
    <meta name="theme-color" content="#050807">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-ink-deep text-ink">
    <header class="border-b border-line bg-surface scan-edge">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-mark.svg') }}" alt="" class="h-8 w-8 ring-1 ring-accent/30">
                <div>
                    <p class="font-display text-lg text-white">Candidate dashboard</p>
                    <p class="text-xs text-muted">{{ $candidate->name }} · {{ $candidate->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('candidate.logout') }}">@csrf<button class="text-sm uppercase tracking-wider text-accent hover:text-white" type="submit">Sign out</button></form>
        </div>
    </header>
    <main class="mx-auto max-w-5xl px-4 py-8">
        @if(session('success'))
            <div class="mb-4 border border-signal/30 bg-signal/10 p-4 text-sm text-signal">{{ session('success') }}</div>
        @endif
        <div class="grid gap-6">
            @forelse($applications as $application)
                <section class="border border-line bg-surface-2 p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="font-display text-2xl text-white">{{ $application->jobPosting->title }}</h2>
                            <p class="text-sm text-muted">Ref {{ $application->reference }} · {{ $application->created_at->format('d M Y') }}</p>
                        </div>
                        <span class="border border-accent/40 bg-accent/10 px-2 py-1 text-xs font-medium uppercase tracking-wide text-accent">{{ str_replace('_', ' ', $application->status) }}</span>
                    </div>
                    <ol class="mt-6 flex flex-wrap gap-2 text-xs">
                        @foreach(['submitted','under_review','docs_verified','hired'] as $step)
                            <li class="rounded-sm px-2 py-1 {{ $application->status === $step || ($step === 'docs_verified' && $application->status === 'hired') ? 'bg-accent text-ink-deep' : 'border border-line bg-surface text-muted' }}">{{ str_replace('_',' ', $step) }}</li>
                        @endforeach
                        @if($application->status === 'rejected')
                            <li class="rounded-sm bg-rose-500/20 px-2 py-1 text-rose-300">rejected</li>
                        @endif
                    </ol>
                    <h3 class="mt-8 text-sm font-semibold uppercase tracking-wider text-accent">Documents</h3>
                    <div class="mt-3 space-y-3">
                        @foreach($application->documents as $document)
                            <div class="border border-line bg-surface p-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-medium text-white">{{ str_replace('_', ' ', $document->type) }}</p>
                                        <p class="text-xs text-muted">{{ $document->file_name }} · {{ $document->status }}</p>
                                        @if($document->notes)<p class="mt-1 text-xs text-rose-400">{{ $document->notes }}</p>@endif
                                    </div>
                                    @if($document->status === 'rejected')
                                        <form method="POST" action="{{ route('candidate.reupload', $document) }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
                                            @csrf
                                            <input type="file" name="file" accept=".pdf,image/*" required class="text-xs text-muted">
                                            <button class="btn-ink !px-3 !py-1.5 !text-xs" type="submit">Re-upload</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @empty
                <p class="text-muted">No applications yet. <a class="text-accent underline hover:text-white" href="{{ route('careers.index') }}">Browse openings</a>.</p>
            @endforelse
        </div>
    </main>
</body>
</html>
