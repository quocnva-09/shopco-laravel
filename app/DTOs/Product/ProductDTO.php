<?php

declare(strict_types=1);

namespace App\DTOs\Product;

use App\Http\Requests\Product\ProductRequest;

readonly class ProductDTO
{
    public function __construct(
        public ?string $name,
        public ?string $slug,
        public ?float $price,
        public ?int $price_discount,
        public ?string $description,
        public ?int $category_id,
        public ?array $images,
        public ?array $sizes,
        public ?array $colors,
        public ?bool $is_active,
    ) {}

    public static function fromRequest(ProductRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            name: $validated['name'] ?? null,
            slug: $validated['slug'] ?? null,
            price: isset($validated['price']) ? (float) $validated['price'] : null,
            price_discount: isset($validated['price_discount']) ? (int) $validated['price_discount'] : null,
            description: $validated['description'] ?? null,
            category_id: isset($validated['category_id']) ? (int) $validated['category_id'] : null,
            images: $validated['images'] ?? null,
            sizes: $validated['sizes'] ?? null,
            colors: $validated['colors'] ?? null,
            is_active: isset($validated['is_active']) ? (bool) $validated['is_active'] : null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'price_discount' => $this->price_discount,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'sizes' => $this->sizes,
            'colors' => $this->colors,
            'is_active' => $this->is_active,
        ];

        return array_filter($data, fn ($value) => $value !== null);
    }
}
