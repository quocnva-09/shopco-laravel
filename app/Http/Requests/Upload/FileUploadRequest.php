<?php

declare(strict_types=1);

namespace App\Http\Requests\Upload;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // 5MB
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Hình ảnh là bắt buộc.',
            'image.image' => 'File phải là hình ảnh.',
            'image.mimes' => 'File phải có định dạng jpeg, png, jpg, hoặc webp.',
            'image.max' => 'File không được vượt quá 5MB.',
        ];
    }
}
