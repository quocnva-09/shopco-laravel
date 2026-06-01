<?php

declare(strict_types=1);

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProductRequest',
    title: 'Product Request',
    description: 'Product creation and update payload',
    required: ['name', 'price', 'category_id'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Classic T-Shirt'),
        new OA\Property(property: 'slug', type: 'string', nullable: true, example: 'classic-t-shirt'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 150000),
        new OA\Property(property: 'price_discount', type: 'number', format: 'float', nullable: true, example: 120000),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'A comfortable everyday t-shirt'),
        new OA\Property(property: 'category_id', type: 'integer', example: 2),
        new OA\Property(
            property: 'images',
            type: 'array',
            nullable: true,
            items: new OA\Items(type: 'string', example: 'products/xyz.jpg')
        ),
        new OA\Property(property: 'size_ids', type: 'array', nullable: true, items: new OA\Items(type: 'integer', example: 1)),
        new OA\Property(property: 'color_ids', type: 'array', nullable: true, items: new OA\Items(type: 'integer', example: 1)),
        new OA\Property(property: 'is_active', type: 'boolean', nullable: true, example: true),
    ]
)]
class ProductRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $productId = $this->route('product') ?? null;

        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $productId,
            'price' => 'required|numeric|min:0',
            'price_discount' => 'nullable|numeric|min:0|lte:price',
            'description' => 'nullable|string',
            'category_id' => 'required|integer|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'size_ids' => 'nullable|array',
            'size_ids.*' => 'integer|exists:sizes,id',
            'color_ids' => 'nullable|array',
            'color_ids.*' => 'integer|exists:colors,id',
            'is_active' => 'nullable|boolean',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['name'] = 'sometimes|required|string|max:255';
            $rules['price'] = 'sometimes|required|numeric|min:0';
            $rules['category_id'] = 'sometimes|required|integer|exists:categories,id';
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->has('images') && is_string($this->images)) {
            $imagesArray = array_map('trim', explode(',', $this->images));

            $this->merge([
                'images' => $imagesArray,
            ]);
        }
    }
}
