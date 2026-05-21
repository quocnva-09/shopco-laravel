<?php

declare(strict_types=1);

namespace App\Http\Requests\Export;

use App\Enums\ExportStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ExportRequest',
    title: 'Export Request',
    description: 'Payload to trigger a new product export job',
    properties: [
        new OA\Property(
            property: 'format',
            type: 'string',
            enum: ['csv', 'xlsx'],
            example: 'csv'
        ),
        new OA\Property(property: 'search', type: 'string', nullable: true, example: 'shirt'),
        // new OA\Property(
        //     property: 'status',
        //     type: 'string',
        //     nullable: true,
        //     enum: ['pending', 'processing', 'completed', 'failed'],
        // ),
    ]
)]
class ExportRequest extends FormRequest
{
    public const EXPORT_FORMATS = ['csv', 'xlsx'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'format' => ['sometimes', Rule::in(self::EXPORT_FORMATS)],
            'search' => ['nullable', 'string'],
            // 'status' => ['nullable', Rule::in(ExportStatus::values())],
        ];
    }
}
