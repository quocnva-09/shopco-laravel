<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'OrderResource',
    title: 'Order Resource',
    description: 'Full order details including line items',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 3),
        new OA\Property(
            property: 'status',
            type: 'string',
            enum: ['pending', 'paid', 'cancelled'],
            example: 'pending'
        ),
        new OA\Property(property: 'totalAmount', type: 'number', format: 'float', example: 450000),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-15 10:30:00'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-15 12:00:00'),
        new OA\Property(
            property: 'items',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/OrderItemResource')
        ),
    ]
)]
class OrderResource extends JsonResource
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
            'user_id' => $this->user_id,
            'status' => $this->status,
            'totalAmount' => (float) $this->totalAmount,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
        ];
    }
}
