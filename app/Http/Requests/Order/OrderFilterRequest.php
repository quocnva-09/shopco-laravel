<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use App\Enums\FilterEnum;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(array_column(OrderStatus::cases(), 'value'))],
            'sort_by' => ['nullable', 'string', Rule::in(FilterEnum::ORDER_SORT)],
            'sort_dir' => ['nullable', 'string', Rule::in(FilterEnum::DIRECTION)],
        ];
    }
}
