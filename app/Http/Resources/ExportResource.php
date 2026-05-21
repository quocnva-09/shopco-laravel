<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ExportResource',
    title: 'Export Resource',
    description: 'An export job record tracking the status and download path of a generated file',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'type', type: 'string', example: 'products'),
        new OA\Property(
            property: 'format',
            type: 'string',
            enum: ['csv', 'xlsx'],
            example: 'csv'
        ),
        new OA\Property(
            property: 'status',
            type: 'string',
            enum: ['pending', 'processing', 'completed', 'failed'],
            example: 'completed'
        ),
        new OA\Property(
            property: 'file_path',
            type: 'string',
            nullable: true,
            example: 'https://s3.amazonaws.com/bucket/exports/products_2024-01-15.csv'
        ),
        new OA\Property(
            property: 'error_message',
            type: 'string',
            nullable: true,
            example: null
        ),
        new OA\Property(
            property: 'created_at',
            type: 'string',
            format: 'date-time',
            nullable: true,
            example: '2024-01-15T10:30:00+07:00'
        ),
    ]
)]
class ExportResource extends JsonResource
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
            'type' => $this->type,
            'format' => $this->format,
            'status' => $this->status,
            'file_path' => $this->file_path ? app(FileUploadService::class)->url($this->file_path) : null,
            'error_message' => $this->error_message,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
