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
        new OA\Property(property: 'user_id', type: 'integer', nullable: true, example: 3),
        new OA\Property(property: 'guest_name', type: 'string', nullable: true, example: 'Nguyen Van A'),
        new OA\Property(property: 'guest_phone', type: 'string', nullable: true, example: '0901234567'),
        new OA\Property(property: 'guest_email', type: 'string', nullable: true, example: 'guest@example.com'),
        new OA\Property(property: 'guest_address', type: 'string', nullable: true, example: '123 Nguyen Trai'),
        new OA\Property(
            property: 'status',
            type: 'string',
            enum: ['pending', 'paid', 'cancelled'],
            example: 'pending'
        ),
        new OA\Property(property: 'totalAmount', type: 'number', format: 'float', example: 450000),
        new OA\Property(property: 'delivery_fee', type: 'number', format: 'float', example: 30000),
        new OA\Property(property: 'discount', type: 'number', format: 'float', example: 10000),
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
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'guest_name'    => $this->guest_name,
            'guest_phone'   => $this->guest_phone,
            'guest_email'   => $this->guest_email,
            'guest_address' => $this->guest_address,
            'status'        => $this->status,
            'totalAmount'   => (float) $this->totalAmount,
            'delivery_fee'  => (float) $this->delivery_fee,
            'discount'      => (float) $this->discount,
            'created_at'    => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'    => $this->updated_at->format('Y-m-d H:i:s'),
            'items'         => OrderItemResource::collection($this->whenLoaded('orderItems')),
        ];
    }
}
