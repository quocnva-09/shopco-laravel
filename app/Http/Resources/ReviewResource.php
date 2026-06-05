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
        new OA\Property(property: "user_id", type: "integer", nullable: true, example: 1),
        new OA\Property(property: "reviewer_name", type: "string", example: "John Doe"),
        new OA\Property(property: "is_guest", type: "boolean", example: false),
        new OA\Property(property: "guest_name", type: "string", nullable: true, example: "Nguyen Van A"),
        new OA\Property(property: "guest_email", type: "string", nullable: true, example: "guest@example.com"),
        new OA\Property(property: "product_id", type: "integer", example: 1),
        new OA\Property(property: "order_item_id", type: "integer", nullable: true, example: 10),
        new OA\Property(property: "rating", type: "integer", example: 5),
        new OA\Property(property: "comment", type: "string", nullable: true, example: "Great product!"),
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
        $isGuest = $this->user_id === null;

        return [
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'reviewer_name' => $isGuest
                ? ($this->guest_name ?? 'Anonymous')
                : ($this->user?->name ?? 'Anonymous'),
            'is_guest'      => $isGuest,
            'guest_name'    => $this->guest_name,
            'guest_email'   => $this->guest_email,
            'product_id'    => $this->product_id,
            'order_item_id' => $this->order_item_id,
            'rating'        => $this->rating,
            'comment'       => $this->comment,
            'is_approved'   => (bool) $this->is_approved,
            'is_verified'   => ! $isGuest && $this->user?->email_verified_at !== null,
            'created_at'    => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'    => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
