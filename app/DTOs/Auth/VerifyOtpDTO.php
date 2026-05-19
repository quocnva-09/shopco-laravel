<?php

namespace App\DTOs\Auth;

use Illuminate\Foundation\Http\FormRequest;

readonly class VerifyOtpDTO
{
    public function __construct(
        public string $otp,
        public string $type,
        public string $email,
        public ?string $password = null
    ) {
    }

    /**
     * Khởi tạo DTO từ Form Request
     */
    public static function fromRequest(FormRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            otp: $validated['otp'],
            type: $validated['type'],
            email: $validated['email'],
            password: $validated['password'] ?? null
        );
    }
}
