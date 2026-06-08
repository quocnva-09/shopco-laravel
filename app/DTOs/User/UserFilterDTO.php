<?php

namespace App\DTOs\User;

use App\Enums\FilterEnum;
use Illuminate\Foundation\Http\FormRequest;

readonly class UserFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public int $page = 1,
        public int $perPage = 15,
        public string $sortBy = 'created_at',
        public string $sortDir = 'desc',
    ) {}

    /**
     * Initialise the DTO from a Form Request
     */
    public static function fromRequest(FormRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            search: $validated['search'] ?? null,
            page: (int) ($validated['page'] ?? 1),
            perPage: (int) ($validated['perPage'] ?? 15),
            sortBy: $validated['sort_by'] ?? FilterEnum::USER_SORT[0],
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
}
