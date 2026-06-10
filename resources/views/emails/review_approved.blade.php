@component('mail::message')
# Your Review Has Been {{ $review->is_approved ? 'Approved' : 'Rejected' }}

Hi **{{ $review->user?->name ?? $review->guest_name }}**,

@if($review->is_approved)
Great news! Your review for **{{ $review->product->name }}** has been **approved** and is now visible to other shoppers.
@else
We're sorry to inform you that your review for **{{ $review->product->name }}** has been **rejected** and will not be published.
@endif

@component('mail::panel')
**Rating:** {{ $review->rating }} / 5 ⭐

**Your comment:**
{{ $review->comment }}
@endcomponent

@if($review->is_approved)
Thank you for sharing your experience with the community!
@else
If you believe this is a mistake or have any questions, please contact our support team.
@endif

Regards,<br>
{{ config('app.name') }}
@endcomponent
