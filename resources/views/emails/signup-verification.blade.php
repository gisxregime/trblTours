<x-mail::message>
# Verify your email

We received a signup request for {{ $email }}.

Click the button below to verify your email address and continue to role selection.

<x-mail::button :url="$verificationUrl">
Verify Email
</x-mail::button>

If you did not request this, you can ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
