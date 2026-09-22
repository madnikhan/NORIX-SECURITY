<x-mail::message>
# You're on the roster

Hi {{ $guard->full_name }},

Welcome to {{ config('norix.name') }}. Your staff app is ready — set a password to view shifts, clock in at site, and track your hours.

<x-mail::button :url="$url">
Set password & open staff app
</x-mail::button>

This link is for you only. After you set a password, sign in any time at {{ route('staff.login') }}.

Thanks,<br>
{{ config('norix.name') }} Operations
</x-mail::message>
