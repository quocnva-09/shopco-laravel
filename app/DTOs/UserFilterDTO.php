<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

readonly class UserFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public int $page = 1,
        public int $perPage = 15,
        public string $sort = 'created_at',
        public string $direction = 'desc',
    ) {
    }

    /**
     * Khởi tạo DTO từ Form Request
     */
    public static function fromRequest(FormRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            search: $validated['search'] ?? null,
            page: (int) ($validated['page'] ?? 1),
            perPage: (int) ($validated['perPage'] ?? 15),
            sort: $validated['sort'] ?? 'created_at',
            direction: $validated['direction'] ?? 'desc',
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
