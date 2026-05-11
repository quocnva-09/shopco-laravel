<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ExportStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExportRequest extends FormRequest
{
    public const array EXPORT_FORMATS = ['csv', 'xlsx'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'format' => ['sometimes', Rule::in(self::EXPORT_FORMATS)],
            'search' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(ExportStatus::values())],
        ];
    }
}
