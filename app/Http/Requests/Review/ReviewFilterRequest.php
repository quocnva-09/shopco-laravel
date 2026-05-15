<?php

declare(strict_types=1);

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ReviewFilterRequest",
    properties: [
        new OA\Property(property: "keyword", type: "string", nullable: true),
        new OA\Property(property: "sort_by", type: "string", nullable: true),
        new OA\Property(property: "sort_direction", type: "string", enum: ["asc", "desc"], nullable: true),
        new OA\Property(property: "limit", type: "integer", nullable: true, example: 15),
        new OA\Property(property: "is_approved", type: "boolean", nullable: true)
    ]
)]
class ReviewFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', 'string', 'in:id,created_at,rating'],
            'sort_direction' => ['nullable', 'string', 'in:asc,desc'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'is_approved' => ['nullable', 'boolean'],
        ];
    }
}
