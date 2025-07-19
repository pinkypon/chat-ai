@component('mail::message')
{{-- Logo --}}
# 🔐 Forgot your password?

No worries — it happens!

Click the button below to securely reset your password:

@component('mail::button', ['url' => $url, 'color' => 'blue'])
Reset Password
@endcomponent

This link will expire in **60 minutes**.

If you didn’t request a password reset, you can safely ignore this email.

Thanks,<br>
**ChatAI**
@endcomponent
