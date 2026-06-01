<?php

namespace App\DTOs\Product;

use App\Enums\FilterEnum;
use Illuminate\Foundation\Http\FormRequest;

readonly class ProductFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?int $categoryId = null,
        public ?string $colors = null,
        public ?string $sizes = null,
        public ?int $minPrice = null,
        public ?int $maxPrice = null,
        public int $page = 1,
        public int $perPage = 15,
        public string $sortBy = 'created_at',
        public string $sortDir = 'desc',
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
            colors: $validated['colors'] ?? null,
            sizes: $validated['sizes'] ?? null,
            minPrice: isset($validated['min_price']) ? (int) $validated['min_price'] : null,
            maxPrice: isset($validated['max_price']) ? (int) $validated['max_price'] : null,
            page: $validated['page'] ?? 1,
            perPage: $validated['per_page'] ?? 15,
            sortBy: $validated['sort_by'] ?? FilterEnum::PRODUCT_SORT[0],
            sortDir: $validated['sort_dir'] ?? FilterEnum::DIRECTION[0],
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
            'colors' => $this->colors,
            'sizes' => $this->sizes,
            'min_price' => $this->minPrice,
            'max_price' => $this->maxPrice,
            'page' => $this->page,
            'per_page' => $this->perPage,
            'sort_by' => $this->sortBy,
            'sort_dir' => $this->sortDir,
        ];
    }

    public function toCacheKey(): string
    {
        return md5(json_encode([
            'search' => $this->search,
            'category_id' => $this->categoryId,
            'colors' => $this->colors,
            'sizes' => $this->sizes,
            'min_price' => $this->minPrice,
            'max_price' => $this->maxPrice,
            'page' => $this->page,
            'per_page' => $this->perPage,
            'sort_by' => $this->sortBy,
            'sort_dir' => $this->sortDir,
        ]));
    }
}
