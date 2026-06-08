<?php

namespace App\Http\Requests\Category;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "CategoryRequest",
    title: "Category Request",
    description: "Category creation and update payload",
    required: ["name", "slug"],
    properties: [
        new OA\Property(property: "name", type: "string", example: "Electronics"),
        new OA\Property(property: "slug", type: "string", example: "electronics"),
        new OA\Property(property: "description", type: "string", nullable: true, example: "Electronic items and gadgets")
    ]
)]
class CategoryRequest extends FormRequest
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
    public function rules()
    {
        $categoryId = $this->route('category') ?? null;

        // POST
        if ($this->isMethod('post')) {
            return [
                'name' => 'required|string|min:3|max:100|unique:categories,name',
                'slug' => 'required|string|max:100|alpha_dash|unique:categories,slug',
                'description' => 'nullable|string',
            ];
        }

        // PUT/PATCH (update)
        return [
            'name' => 'required|string|min:3|max:100|unique:categories,name,'.$categoryId,
            'slug' => 'required|string|max:100|alpha_dash|unique:categories,slug,'.$categoryId,
            'description' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required'     => 'The category name is required.',
            'name.min'          => 'The category name must be at least 3 characters.',
            'name.max'          => 'The category name may not exceed 100 characters.',
            'name.unique'       => 'The category name already exists.',
            'slug.required'     => 'The category slug is required.',
            'slug.max'          => 'The category slug may not exceed 100 characters.',
            'slug.alpha_dash'   => 'The category slug may only contain letters, numbers, dashes, and underscores.',
            'slug.unique'       => 'The category slug already exists.',
            'description.string' => 'The category description must be a string.',
        ];
    }
}
