<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use App\Services\FileUploadService;

#[OA\Schema(
    schema: 'ProductImageResource',
    title: 'ProductImageResource',
    description: 'Product image representation',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'product_id', type: 'integer', example: 5),
        new OA\Property(property: 'img_path', type: 'string', nullable: true, example: '/storage/products/shirt.jpg'),
        new OA\Property(property: 'alt', type: 'string', nullable: true, example: 'Front view of shirt'),
        new OA\Property(property: 'is_primary', type: 'boolean', example: true),
    ]
)]
class ProductImageResource extends JsonResource
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
            'product_id' => $this->product_id,
            'img_path' => $this->img_path ? app(FileUploadService::class)->url($this->img_path) : null,
            'alt' => $this->alt,
            'is_primary' => $this->is_primary,
        ];
    }
}
