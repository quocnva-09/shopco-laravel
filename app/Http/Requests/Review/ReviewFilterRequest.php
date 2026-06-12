<?php

declare(strict_types=1);

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ReviewFilterRequest",
    properties: [
        new OA\Property(property: "product_id", type: "integer", nullable: true),
        new OA\Property(property: "keyword", type: "string", nullable: true),
        new OA\Property(property: "sort_by", type: "string", nullable: true),
        new OA\Property(property: "sort_dir", type: "string", enum: ["asc", "desc"], nullable: true),
        new OA\Property(property: "limit", type: "integer", nullable: true, example: 15),
        new OA\Property(property: "is_approved", type: "boolean", nullable: true),
        new OA\Property(property: "rating", type: "number", format: "float", nullable: true, minimum: 1, maximum: 5, example: 4.5),
    ]
)]
class ReviewFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_approved')) {
            $this->merge([
                'is_approved' => filter_var($this->input('is_approved'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'keyword' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', 'string', 'in:id,created_at,rating'],
            'sort_dir' => ['nullable', 'string', 'in:asc,desc'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'is_approved' => ['nullable', 'boolean'],
            'rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
        ];
    }
}
