<?php

declare(strict_types=1);

namespace App\DTOs\Review;

use App\Http\Requests\Review\ReviewFilterRequest;

class ReviewFilterDTO
{
    public function __construct(
        public readonly ?int $productId = null,
        public readonly ?string $keyword = null,
        public readonly ?string $sortBy = null,
        public readonly ?string $sortDir = null,
        public readonly int $limit = 15,
        public readonly ?bool $isApproved = null
    ) {
    }

    public static function fromRequest(ReviewFilterRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            productId: isset($validated['product_id']) ? (int) $validated['product_id'] : null,
            keyword: $validated['keyword'] ?? null,
            sortBy: $validated['sort_by'] ?? null,
            sortDir: $validated['sort_dir'] ?? null,
            limit: isset($validated['limit']) ? (int) $validated['limit'] : 15,
            isApproved: isset($validated['is_approved']) ? (bool) $validated['is_approved'] : null
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'keyword' => $this->keyword,
            'sort_by' => $this->sortBy,
            'sort_dir' => $this->sortDir,
            'limit' => $this->limit,
            'is_approved' => $this->isApproved,
        ];
    }
}
