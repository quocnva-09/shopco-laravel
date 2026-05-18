<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'OrderItemResource',
    title: 'Order Item Resource',
    description: 'A single line item within an order, including a product snapshot',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'quantity', type: 'integer', example: 2),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 150000),
        new OA\Property(property: 'totalMoney', type: 'number', format: 'float', example: 300000),
        new OA\Property(
            property: 'options',
            type: 'object',
            nullable: true,
            properties: [
                new OA\Property(property: 'sizes', type: 'string', example: 'M'),
                new OA\Property(property: 'colors', type: 'string', example: 'Red'),
            ]
        ),
        new OA\Property(
            property: 'product',
            type: 'object',
            nullable: true,
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 7),
                new OA\Property(property: 'name', type: 'string', example: 'Classic T-Shirt'),
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
class OrderItemResource extends JsonResource
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
            'quantity' => $this->quantity,
            'price' => (float) $this->price,
            'totalMoney' => (float) $this->totalMoney,
            'options' => $this->options,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
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
