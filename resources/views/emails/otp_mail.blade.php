@component('mail::message')
# Your OTP Code

Use the following OTP code to proceed with your request. This code is valid for a short period of time.

@component('mail::panel')
<div style="text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px;">
{{ $otp }}
</div>
@endcomponent

If you did not request this password reset or registration, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
