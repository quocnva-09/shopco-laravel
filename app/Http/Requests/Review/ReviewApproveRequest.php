<?php

declare(strict_types=1);

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ReviewApproveRequest",
    required: ["status"],
    properties: [
        new OA\Property(property: "status", type: "string", example: "approved")
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
            'status' => ['required', 'string', \Illuminate\Validation\Rule::enum(\App\Enums\ReviewStatus::class)],
        ];
    }
}
