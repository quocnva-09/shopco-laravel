<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "VerifyGuestOrderOtpRequest",
    required: ["otp"],
    properties: [
        new OA\Property(property: "otp", type: "string", description: "The 6-digit OTP sent to the user's email", example: "123456")
    ]
)]
class VerifyGuestOrderOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'otp' => ['required', 'string', 'numeric', 'digits:6'],
        ];
    }
}
