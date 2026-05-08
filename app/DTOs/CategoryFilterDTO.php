<?php

namespace App\DTOs;

use App\Enums\FilterEnum;
use Illuminate\Foundation\Http\FormRequest;

readonly class CategoryFilterDTO
{
    public function __construct(
        public ?string $search = null,
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
            page: $validated['page'] ?? 1,
            perPage: $validated['perPage'] ?? 15,
            sort: $validated['sort'] ?? FilterEnum::CATEGORY_SORT[0],
            direction: $validated['direction'] ?? FilterEnum::DIRECTION[0],
        );
    }

    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'page' => $this->page,
            'perPage' => $this->perPage,
            'sort' => $this->sort,
            'direction' => $this->direction,
        ];
    }
}
