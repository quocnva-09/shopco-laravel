<?php

declare(strict_types=1);

namespace App\DTOs\Review;

use App\Http\Requests\Review\ReviewApproveRequest;

class ReviewApproveDTO
{
    public function __construct(
        public readonly string $status
    ) {
    }

    public static function fromRequest(ReviewApproveRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            status: $validated['status']
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
        ];
    }
}
