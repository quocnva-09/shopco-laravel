<?php

namespace App\Http\Resources;

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
        new OA\Property(property: 'quantity', type: 'integer', example: 2),
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
                new OA\Property(
                    property: 'images',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(
                                property: 'url',
                                type: 'string',
                                example: 'https://example.com/images/shirt.jpg'
                            ),
                        ]
                    )
                ),
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
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'price' => $this->product->price,
                    'price_discount' => $this->product->price_discount,
                    'images' => $this->product->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->url,
                        ];
                    }),
                ];
            }),
        ];
    }
}
