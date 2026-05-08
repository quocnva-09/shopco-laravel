<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\TemplateRequest;

readonly class TemplateDTO
{
    public function __construct(
        public string $name,
        public ?string $description
    ) {}

    public static function fromRequest(TemplateRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            name: $validated['name'],
            description: $validated['description'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
