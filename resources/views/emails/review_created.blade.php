@component('mail::message')
# Thank you for your review!

Hi **{{ $review->user?->name ?? $review->guest_name }}**,

Your review for **{{ $review->product->name }}** has been successfully submitted and is now **pending approval** by our team.

@component('mail::panel')
**Rating:** {{ $review->rating }} / 5 ⭐

**Your comment:**
{{ $review->comment }}
@endcomponent

We will notify you once your review has been reviewed. This usually takes less than 24 hours.

Thanks for helping the community!

Regards,<br>
{{ config('app.name') }}
@endcomponent
