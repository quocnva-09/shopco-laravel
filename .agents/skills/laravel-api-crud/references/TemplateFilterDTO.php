<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\TemplateFilterRequest;

readonly class TemplateFilterDTO
{
    public function __construct(
        public ?string $keyword,
        public ?string $sort,
        public ?string $order,
        public int $perPage
    ) {}

    public static function fromRequest(TemplateFilterRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            keyword: $validated['keyword'] ?? null,
            sort: $validated['sort'] ?? null,
            order: $validated['order'] ?? null,
            perPage: (int) ($validated['per_page'] ?? 15)
        );
    }
}
