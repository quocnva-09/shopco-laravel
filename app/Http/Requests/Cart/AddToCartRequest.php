<?php

namespace App\Http\Requests\Cart;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AddToCartRequest',
    title: 'Add To Cart Request',
    description: 'Payload to add a product to the authenticated user\'s cart',
    required: ['product_id', 'quantity'],
    properties: [
        new OA\Property(property: 'product_id', type: 'integer', example: 7),
        new OA\Property(property: 'quantity', type: 'integer', minimum: 1, example: 2),
        new OA\Property(
            property: 'options',
            type: 'object',
            nullable: true,
            properties: [
                new OA\Property(property: 'sizes', type: 'string', example: 'M'),
                new OA\Property(property: 'colors', type: 'string', example: 'Red'),
            ]
        ),
    ]
)]
class AddToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'options' => ['nullable', 'array'],
        ];

        foreach (\App\Enums\ProductVariants::cases() as $variant) {
            $rules['options.' . $variant->value] = ['nullable', 'string'];
        }

        return $rules;
    }
}
