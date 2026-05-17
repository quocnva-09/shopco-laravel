<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserResource',
    title: 'UserResource',
    description: 'User resource representation',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
        new OA\Property(property: 'role', type: 'string', example: 'user'),
        new OA\Property(property: 'profile_image', type: 'string', example: 'https://s3.amazonaws.com/bucket/users/avatar.jpg', nullable: true),
        new OA\Property(property: 'address', type: 'string', example: '123 Main St', nullable: true),
        new OA\Property(property: 'phone', type: 'string', example: '1234567890', nullable: true),
        new OA\Property(property: 'bio', type: 'string', example: 'I am a user', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2023-01-01 12:00:00'),
    ]
)]
class UserResource extends JsonResource
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
            'email' => $this->email,
            'role' => $this->role,
            'profile_image' => $this->profile_image ? app(\App\Services\FileUploadService::class)->url($this->profile_image) : null,
            'address' => $this->address,
            'phone' => $this->phone,
            'bio' => $this->bio,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
