<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProductResource',
    title: 'ProductResource',
    description: 'Product resource representation',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Classic T-Shirt'),
        new OA\Property(property: 'slug', type: 'string', example: 'classic-t-shirt'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'A comfortable everyday t-shirt'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 150000),
        new OA\Property(property: 'price_discount', type: 'number', format: 'float', nullable: true, example: 120000),
        new OA\Property(
            property: 'sizes',
            type: 'array',
            nullable: true,
            items: new OA\Items(properties: [
                new OA\Property(property: 'size', type: 'string', example: 'S'),
                new OA\Property(property: 'label', type: 'string', example: 'Small'),
            ], type: 'object')
        ),
        new OA\Property(
            property: 'colors',
            type: 'array',
            nullable: true,
            items: new OA\Items(properties: [
                new OA\Property(property: 'color', type: 'string', example: 'Red'),
                new OA\Property(property: 'hex', type: 'string', example: '#ff0000'),
            ], type: 'object')
        ),
        new OA\Property(property: 'is_active', type: 'boolean', example: true),
        new OA\Property(property: 'rating_avg', type: 'number', format: 'float', nullable: true, example: 4.5),
        new OA\Property(property: 'reviews_count', type: 'integer', nullable: true, example: 10),
        new OA\Property(property: 'sold_count', type: 'integer', nullable: true, example: 50),
        new OA\Property(
            property: 'category',
            type: 'object',
            nullable: true,
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 2),
                new OA\Property(property: 'name', type: 'string', example: 'T-Shirts'),
                new OA\Property(property: 'slug', type: 'string', example: 't-shirts'),
            ]
        ),
        new OA\Property(
            property: 'images',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/ProductImageResource')
        ),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2023-01-01 12:00:00'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2023-01-01 12:00:00'),
    ]
)]
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'price_discount' => $this->price_discount,
            'sizes' => $this->whenLoaded('sizes', function () {
                return $this->sizes->map(function ($size) {
                    return [
                        'size' => $size->name,
                        'label' => $size->label,
                    ];
                });
            }),
            'colors' => $this->whenLoaded('colors', function () {
                return $this->colors->map(function ($color) {
                    return [
                        'color' => $color->name,
                        'hex' => $color->hex_code,
                    ];
                });
            }),
            'is_active' => (bool)$this->is_active,
            $this->mergeWhen(array_key_exists('approved_reviews_avg_rating', $this->getAttributes()), [
                'rating_avg' => $this->approved_reviews_avg_rating !== null ? round((float) $this->approved_reviews_avg_rating, 1) : 0,
                'reviews_count' => (int) $this->approved_reviews_count,
            ]),
            $this->mergeWhen(array_key_exists('sold_count', $this->getAttributes()), [
                'sold_count' => (int) $this->sold_count,
            ]),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
