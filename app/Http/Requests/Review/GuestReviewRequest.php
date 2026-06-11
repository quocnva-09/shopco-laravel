<?php

declare(strict_types=1);

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'GuestReviewRequest',
    title: 'Guest Review Request',
    description: 'Payload for submitting a guest review. order_id is provided from the order confirmation email.',
    required: ['order_id', 'product_id', 'rating'],
    properties: [
        new OA\Property(property: 'order_id', type: 'integer', example: 5),
        new OA\Property(property: 'product_id', type: 'integer', example: 7),
        new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5, example: 5),
        new OA\Property(property: 'comment', type: 'string', nullable: true, example: 'Great product!'),
        new OA\Property(property: 'guest_name', type: 'string', nullable: true, example: 'Nguyen Van A'),
        new OA\Property(property: 'guest_email', type: 'string', format: 'email', nullable: true, example: 'guest@example.com'),
    ]
)]
class GuestReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'order_id'    => ['required', 'integer', 'exists:orders,id'],
            'product_id'  => ['required', 'integer', 'exists:products,id'],
            'rating'      => ['required', 'integer', 'between:1,5'],
            'comment'     => ['nullable', 'string', 'max:500'],
            'guest_name'  => ['nullable', 'string', 'max:50'],
            'guest_email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
