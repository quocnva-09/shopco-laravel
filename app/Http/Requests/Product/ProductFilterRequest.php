<?php

namespace App\Http\Requests\Product;

use App\Enums\FilterEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductFilterRequest extends FormRequest
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
            'search' => ['nullable', 'string', 'max:255'],
            'colors' => ['nullable', 'array'],
            'colors.*' => ['string', 'max:255'],
            'sizes' => ['nullable', 'array'],
            'sizes.*' => ['string', 'max:255'],
            'style_ids' => ['nullable', 'array'],
            'style_ids.*' => ['integer', 'exists:styles,id'],
            'style_slugs' => ['nullable', 'array'],
            'style_slugs.*' => ['string', 'exists:styles,slug'],
            'min_price' => ['nullable', 'integer', 'min:0'],
            'max_price' => ['nullable', 'integer', 'gte:min_price'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'category_slug' => ['nullable', 'string', 'exists:categories,slug'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['nullable', 'string', Rule::in(FilterEnum::PRODUCT_SORT)],
            'sort_dir' => ['nullable', 'string', Rule::in(FilterEnum::DIRECTION)],
        ];
    }
}
