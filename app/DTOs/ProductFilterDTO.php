<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

readonly class ProductFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?int $categoryId = null,
        public int $page = 1,
        public int $perPage = 15,
        public string $sort = 'created_at',
        public string $direction = 'desc',
    ) {}

    /**
     * Khởi tạo DTO từ Form Request
     */
    public static function fromRequest(FormRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            search: $validated['search'] ?? null,
            categoryId: $validated['category_id'] ?? null,
            page: $validated['page'] ?? 1,
            perPage: $validated['perPage'] ?? 15,
            sort: $validated['sort'] ?? 'created_at',
            direction: $validated['direction'] ?? 'desc',
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'category_id' => $this->categoryId,
            'page' => $this->page,
            'perPage' => $this->perPage,
            'sort' => $this->sort,
            'direction' => $this->direction,
        ];
    }
}
