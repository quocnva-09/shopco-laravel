<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'file_path' => $this->file_path,
            'error_message' => $this->error_message,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
