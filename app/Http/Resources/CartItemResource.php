<?php

namespace App\Http\Resources;

use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CartItemResource',
    title: 'Cart Item Resource',
    description: 'A single item inside the cart with a product snapshot',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'cart_id', type: 'integer', example: 3),
        new OA\Property(property: 'product_id', type: 'integer', example: 7),
        new OA\Property(property: 'product_variant_id', type: 'integer', nullable: true, example: 2),
        new OA\Property(property: 'quantity', type: 'integer', example: 2),
        new OA\Property(
            property: 'variant',
            type: 'object',
            nullable: true,
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 2),
                new OA\Property(property: 'color', type: 'string', nullable: true, example: 'Red'),
                new OA\Property(property: 'color_hex', type: 'string', nullable: true, example: '#ff0000'),
                new OA\Property(property: 'size', type: 'string', nullable: true, example: 'M'),
                new OA\Property(property: 'size_label', type: 'string', nullable: true, example: 'Medium'),
            ]
        ),
        new OA\Property(
            property: 'product',
            type: 'object',
            nullable: true,
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 7),
                new OA\Property(property: 'name', type: 'string', example: 'Classic T-Shirt'),
                new OA\Property(property: 'price', type: 'number', format: 'float', example: 150000),
                new OA\Property(
                    property: 'price_discount',
                    type: 'number',
                    format: 'float',
                    nullable: true,
                    example: 120000
                ),
                new OA\Property(property: 'img_path', type: 'string', nullable: true, example: 'https://example.com/images/shirt.jpg'),
            ]
        ),
    ]
)]
class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'cart_id'            => $this->cart_id,
            'product_id'         => $this->product_id,
            'product_variant_id' => $this->product_variant_id,
            'quantity'           => $this->quantity,
            'variant'            => $this->whenLoaded('productVariant', function () {
                if (! $this->productVariant) {
                    return null;
                }

                return [
                    'id'         => $this->productVariant->id,
                    'color'      => $this->productVariant->color?->name,
                    'color_hex'  => $this->productVariant->color?->hex_code,
                    'size'       => $this->productVariant->size?->name,
                    'size_label' => $this->productVariant->size?->label,
                ];
            }),
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id'             => $this->product->id,
                    'name'           => $this->product->name,
                    'price'          => $this->product->price,
                    'price_discount' => $this->product->price_discount,
                    'img_path'       => app(FileUploadService::class)->url($this->product->images[0]->img_path) ?? null,
                ];
            }),
        ];
    }
}
