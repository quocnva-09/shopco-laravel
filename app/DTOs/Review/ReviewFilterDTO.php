<?php

declare(strict_types=1);

namespace App\DTOs\Review;

use App\Http\Requests\Review\ReviewFilterRequest;

class ReviewFilterDTO
{
    public function __construct(
        public readonly ?string $keyword = null,
        public readonly ?string $sortBy = null,
        public readonly ?string $sortDirection = null,
        public readonly int $limit = 15,
        public readonly ?bool $isApproved = null
    ) {
    }

    public static function fromRequest(ReviewFilterRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            keyword: $validated['keyword'] ?? null,
            sortBy: $validated['sort_by'] ?? null,
            sortDirection: $validated['sort_direction'] ?? null,
            limit: isset($validated['limit']) ? (int) $validated['limit'] : 15,
            isApproved: isset($validated['is_approved']) ? (bool) $validated['is_approved'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'keyword' => $this->keyword,
            'sort_by' => $this->sortBy,
            'sort_direction' => $this->sortDirection,
            'limit' => $this->limit,
            'is_approved' => $this->isApproved,
        ];
    }
}
