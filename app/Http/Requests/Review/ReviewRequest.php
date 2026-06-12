<?php

declare(strict_types=1);

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ReviewRequest",
    required: ["product_id", "order_item_id", "rating", "comment"],
    properties: [
        new OA\Property(property: "product_id", type: "integer", example: 1),
        new OA\Property(property: "order_item_id", type: "integer", example: 10),
        new OA\Property(property: "rating", type: "number", format: "float", minimum: 1, maximum: 5, example: 4.5),
        new OA\Property(property: "comment", type: "string", example: "Great product!")
    ]
)]
class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'order_item_id' => ['required', 'integer', 'exists:order_items,id'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ];
    }
}
