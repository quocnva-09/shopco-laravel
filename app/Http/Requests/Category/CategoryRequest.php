<?php

namespace App\Http\Requests\Category;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
            'name.required' => 'Tên danh mục bắt buộc.',
            'name.min' => 'Tên danh mục phải có ít nhất 3 ký tự.',
            'name.max' => 'Tên danh mục không được vượt quá 100 ký tự.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'slug.required' => 'Slug danh mục bắt buộc.',
            'slug.max' => 'Slug danh mục không được vượt quá 100 ký tự.',
            'slug.alpha_dash' => 'Slug danh mục chỉ được chứa chữ cái, số, gạch ngang và gạch dưới.',
            'slug.unique' => 'Slug danh mục đã tồn tại.',
            'description.string' => 'Mô tả danh mục phải là chuỗi.',
        ];
    }
}
