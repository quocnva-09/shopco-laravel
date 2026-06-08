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
            'image.required' => 'An image is required.',
            'image.image'    => 'The file must be an image.',
            'image.mimes'    => 'The file must be of type: jpeg, png, jpg, or webp.',
            'image.max'      => 'The file may not be larger than 5MB.',
        ];
    }
}
