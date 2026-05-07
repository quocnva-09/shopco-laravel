<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

readonly class CategoryDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description = null,
    ) {
    }

    /**
     * Khởi tạo DTO từ Form Request
     */

    // TO FIX: FORM REQUEST THROW EXCEPTION WHEN CONTROLLER CALL THIS METHOD
    public static function fromRequest(FormRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            name: $validated['name'],
            slug: $validated['slug'],
            description: $validated['description'] ?? null,
        );
    }
}