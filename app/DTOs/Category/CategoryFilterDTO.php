<?php

namespace App\DTOs\Category;

use App\Enums\FilterEnum;
use Illuminate\Foundation\Http\FormRequest;

readonly class CategoryFilterDTO
{
    public function __construct(
        public ?string $search = null,
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
            page: $validated['page'] ?? 1,
            perPage: $validated['perPage'] ?? 15,
            sortBy: $validated['sort_by'] ?? FilterEnum::CATEGORY_SORT[0],
            sortDir: $validated['sort_dir'] ?? FilterEnum::DIRECTION[0],
        );
    }

    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'page' => $this->page,
            'perPage' => $this->perPage,
            'sort_by' => $this->sortBy,
            'sort_dir' => $this->sortDir,
        ];
    }

    public function toCacheKey(): string
    {
        return md5(json_encode([
            'search' => $this->search,
            'page' => $this->page,
            'per_page' => $this->perPage,
            'sort_by' => $this->sortBy,
            'sort_dir' => $this->sortDir,
        ]));
    }
}
