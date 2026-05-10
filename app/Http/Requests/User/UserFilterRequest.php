<?php

namespace App\Http\Requests\User;

use App\Enums\FilterEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserFilterRequest extends FormRequest
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
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['nullable', 'string', Rule::in(FilterEnum::USER_SORT)],
            'sort_dir' => ['nullable', 'string', Rule::in(FilterEnum::DIRECTION)],
        ];
    }
}
