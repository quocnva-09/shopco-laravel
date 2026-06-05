<?php

namespace App\Http\Requests\Cart;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AddToCartRequest',
    title: 'Add To Cart Request',
    description: 'Payload to add a product to the authenticated user\'s cart. '
        . 'Use product_variant_id (Mode 1) OR color_id/size_id (Mode 2).',
    required: ['product_id', 'quantity'],
    properties: [
        new OA\Property(property: 'product_id', type: 'integer', example: 7),
        new OA\Property(property: 'quantity', type: 'integer', minimum: 1, example: 2),
        new OA\Property(
            property: 'product_variant_id',
            type: 'integer',
            nullable: true,
            description: 'Mode 1: pass the variant ID directly',
            example: 3
        ),
        new OA\Property(
            property: 'color_id',
            type: 'integer',
            nullable: true,
            description: 'Mode 2: color ID, server resolves to variant',
            example: 1
        ),
        new OA\Property(
            property: 'size_id',
            type: 'integer',
            nullable: true,
            description: 'Mode 2: size ID, server resolves to variant',
            example: 2
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
        return [
            'product_id'         => ['required', 'integer', 'exists:products,id'],
            'quantity'           => ['required', 'integer', 'min:1'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'color_id'           => ['nullable', 'integer', 'exists:colors,id'],
            'size_id'            => ['nullable', 'integer', 'exists:sizes,id'],
        ];
    }
}
