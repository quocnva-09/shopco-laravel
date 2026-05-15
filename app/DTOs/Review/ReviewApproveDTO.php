<?php

declare(strict_types=1);

namespace App\DTOs\Review;

use App\Http\Requests\Review\ReviewApproveRequest;

class ReviewApproveDTO
{
    public function __construct(
        public readonly bool $isApproved
    ) {
    }

    public static function fromRequest(ReviewApproveRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            isApproved: (bool) $validated['is_approved']
        );
    }

    public function toArray(): array
    {
        return [
            'is_approved' => $this->isApproved,
        ];
    }
}
