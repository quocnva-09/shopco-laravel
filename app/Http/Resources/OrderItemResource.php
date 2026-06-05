<?php

namespace App\Http\Resources;

use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'OrderItemResource',
    title: 'Order Item Resource',
    description: 'A single line item within an order, including a product snapshot',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'product_variant_id', type: 'integer', nullable: true, example: 2),
        new OA\Property(property: 'product_name', type: 'string', nullable: true, example: 'Classic T-Shirt'),
        new OA\Property(property: 'product_variant_name', type: 'string', nullable: true, example: 'Red / M'),
        new OA\Property(property: 'quantity', type: 'integer', example: 2),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 150000),
        new OA\Property(property: 'totalMoney', type: 'number', format: 'float', example: 300000),
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
                                property: 'img_path',
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
            'id'                   => $this->id,
            'product_variant_id'   => $this->product_variant_id,
            'product_name'         => $this->product_name,
            'product_variant_name' => $this->product_variant_name,
            'quantity'             => $this->quantity,
            'price'                => (float) $this->price,
            'totalMoney'           => (float) $this->totalMoney,
            'product'              => $this->whenLoaded('product', function () {
                return [
                    'id'       => $this->product->id,
                    'name'     => $this->product->name,
                    'img_path' => app(FileUploadService::class)->url($this->product->images[0]->img_path),
                ];
            }),
        ];
    }
}
