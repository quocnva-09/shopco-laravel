<?php

declare(strict_types=1);

namespace App\DTOs\Review;

use App\Http\Requests\Review\ReviewRequest;

class ReviewDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly int $productId,
        public readonly int $orderItemId,
        public readonly int $rating,
        public readonly string $comment,
        public readonly bool $isApproved = false
    ) {
    }

    public static function fromRequest(ReviewRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            userId: auth()->id(),
            productId: (int) $validated['product_id'],
            orderItemId: (int) $validated['order_item_id'],
            rating: (int) $validated['rating'],
            comment: $validated['comment'],
            isApproved: false
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'product_id' => $this->productId,
            'order_item_id' => $this->orderItemId,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'is_approved' => $this->isApproved,
        ];
    }
}
