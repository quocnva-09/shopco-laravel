<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TemplateResource',
    title: 'TemplateResource',
    description: 'Template resource representation',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Sample Name'),
        new OA\Property(property: 'description', type: 'string', example: 'Sample Description'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2023-01-01 12:00:00'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2023-01-01 12:00:00'),
    ]
)]
class TemplateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
