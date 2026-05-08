<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Example check: return $this->user()->isAdmin();
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name' => ['required', 'string', 'max:255', 'unique:templates,name'],
                'description' => ['nullable', 'string'],
            ];
        }

        // For PUT/PATCH
        return [
            'name' => [
                'sometimes', 
                'string', 
                'max:255', 
                Rule::unique('templates', 'name')->ignore($this->route('template')),
            ],
            'description' => ['nullable', 'string'],
        ];
    }
}
