<?php

declare(strict_types=1);

namespace App\DTOs\Review;

use App\Http\Requests\Review\GuestReviewRequest;

readonly class GuestReviewDTO
{
    public function __construct(
        public int $orderId,
        public int $productId,
        public int $rating,
        public ?string $comment,
        public ?string $guestName,
        public ?string $guestEmail,
    ) {}

    public static function fromRequest(GuestReviewRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            orderId: (int) $validated['order_id'],
            productId: (int) $validated['product_id'],
            rating: (int) $validated['rating'],
            comment: $validated['comment'] ?? null,
            guestName: $validated['guest_name'] ?? null,
            guestEmail: $validated['guest_email'] ?? null,
        );
    }
}
