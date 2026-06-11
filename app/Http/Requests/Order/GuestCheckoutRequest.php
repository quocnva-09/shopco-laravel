<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'GuestCheckoutRequest',
    title: 'Guest Checkout Request',
    description: 'Payload for guest checkout. Backend re-calculates all prices from DB — never trust FE price/total.',
    required: ['items'],
    properties: [
        new OA\Property(
            property: 'items',
            type: 'array',
            minItems: 1,
            items: new OA\Items(
                required: ['product_id', 'quantity'],
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'product_variant_id',
                        type: 'integer',
                        nullable: true,
                        description: 'Mode 1: pass variant ID directly',
                        example: 2
                    ),
                    new OA\Property(
                        property: 'color_id',
                        type: 'integer',
                        nullable: true,
                        description: 'Mode 2: resolve variant by color + size',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'size_id',
                        type: 'integer',
                        nullable: true,
                        description: 'Mode 2: resolve variant by color + size',
                        example: 3
                    ),
                    new OA\Property(property: 'quantity', type: 'integer', minimum: 1, example: 2),
                ],
                type: 'object'
            )
        ),
        new OA\Property(property: 'delivery_fee', type: 'number', format: 'float', nullable: true, example: 30000),
        new OA\Property(property: 'discount', type: 'number', format: 'float', nullable: true, example: 10000),
        new OA\Property(property: 'guest_name', type: 'string', nullable: true, example: 'Nguyen Van A'),
        new OA\Property(property: 'guest_phone', type: 'string', nullable: true, example: '0901234567'),
        new OA\Property(property: 'guest_email', type: 'string', format: 'email', nullable: true, example: 'guest@example.com'),
        new OA\Property(property: 'guest_address', type: 'string', nullable: true, example: '123 Nguyen Trai, HCM'),
    ]
)]
class GuestCheckoutRequest extends FormRequest
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
            'items'                       => ['required', 'array', 'min:1'],
            'items.*.product_id'          => ['required', 'integer', 'exists:products,id'],
            'items.*.product_variant_id'  => ['nullable', 'integer', 'exists:product_variants,id'],
            'items.*.color_id'            => ['nullable', 'integer', 'exists:colors,id'],
            'items.*.size_id'             => ['nullable', 'integer', 'exists:sizes,id'],
            'items.*.quantity'            => ['required', 'integer', 'min:1'],
            'delivery_fee'                => ['nullable', 'numeric', 'min:0'],
            'discount'                    => ['nullable', 'numeric', 'min:0'],
            'guest_name'                  => ['nullable', 'string', 'max:50'],
            'guest_phone'                 => ['nullable', 'string', 'max:10'],
            'guest_email'                 => ['nullable', 'email', 'max:255'],
            'guest_address'               => ['nullable', 'string', 'max:500'],
        ];
    }
}
