<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'VerifyOtpRequest',
    title: 'Verify Otp Request',
    description: 'User verify otp request body',
    required: ['otp', 'type', 'email'],
    properties: [
        new OA\Property(property: 'otp', type: 'string', example: '123456'),
        new OA\Property(property: 'type', type: 'string', enum: ['forget', 'register'], example: 'forget'),
        new OA\Property(property: 'email', type: 'string', example: 'kenjiprovip@gmail.com'),
        new OA\Property(property: 'password', type: 'string', example: 'password'),
        new OA\Property(property: 'password_confirmation', type: 'string', example: 'password')
    ]
)]
class VerifyOtpRequest extends FormRequest
{

    public const VERIFY_TYPES = [
        'verify_forget_password' => 'forget',
        'verify_register' => 'register'
    ];

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
            'otp' => ['required', 'string', 'min:6', 'max:6'],
            'type' => ['required', Rule::in(self::VERIFY_TYPES)],
            'email' => ['required', 'string', 'email'],
            'password' => ['required_if:type,forget', 'string', 'min:6', 'confirmed'],
            'password_confirmation' => ['required_if:type,forget', 'string', 'min:6']
        ];
    }

    public function messages(): array
    {
        return [
            'otp.required' => 'OTP is required.',
            'otp.string' => 'OTP must be a string.',
            'otp.min' => 'OTP must be at least 6 characters.',
            'otp.max' => 'OTP must be at most 6 characters.',
            'type.required' => 'Type is required.',
            'type.in' => 'Type must be either forget or register.',
            'email.required' => 'Email is required.',
            'email.string' => 'Email must be a string.',
            'email.email' => 'Email must be a valid email address.',
            'password.required_if' => 'Password is required.',
            'password.string' => 'Password must be a string.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
