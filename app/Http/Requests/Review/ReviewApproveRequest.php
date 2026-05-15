<?php

declare(strict_types=1);

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ReviewApproveRequest",
    required: ["is_approved"],
    properties: [
        new OA\Property(property: "is_approved", type: "boolean", example: true)
    ]
)]
class ReviewApproveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_approved' => ['required', 'boolean'],
        ];
    }
}
