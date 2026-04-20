<x-mail::message>
# Verify your login email

We received a login request for {{ $email }}.

Click the button below to verify your email and continue to role selection.

<x-mail::button :url="$verificationUrl">
Verify Login
</x-mail::button>

If you did not request this login, you can ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
