<?php

declare(strict_types=1);

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

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
            'slug' => 'nullable|string|max:255|unique:products,slug,'.$productId,
            'price' => 'required|numeric|min:0',
            'price_discount' => 'nullable|numeric|min:0|lte:price',
            'description' => 'nullable|string',
            'category_id' => 'required|integer|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['name'] = 'sometimes|required|string|max:255';
            $rules['price'] = 'sometimes|required|numeric|min:0';
            $rules['category_id'] = 'sometimes|required|integer|exists:categories,id';
        }

        return $rules;
    }
}
