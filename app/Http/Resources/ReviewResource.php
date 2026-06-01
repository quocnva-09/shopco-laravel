<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ReviewResource",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "user_id", type: "integer", example: 1),
        new OA\Property(property: "user_name", type: "string", example: "John Doe"),
        new OA\Property(property: "product_id", type: "integer", example: 1),
        new OA\Property(property: "order_item_id", type: "integer", example: 10),
        new OA\Property(property: "rating", type: "integer", example: 5),
        new OA\Property(property: "comment", type: "string", example: "Great product!"),
        new OA\Property(property: "is_approved", type: "boolean", example: true),
        new OA\Property(property: "is_verified", type: "boolean", example: true),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
    ]
)]
class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user?->name ?? 'Anonymous User',
            'product_id' => $this->product_id,
            'order_item_id' => $this->order_item_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'is_approved' => (bool) $this->is_approved,
            'is_verified' => $this->user?->email_verified_at !== null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
