<?php

namespace App\DTOs\Auth;

use Illuminate\Foundation\Http\FormRequest;

readonly class ForgetPasswordDTO
{
    public function __construct(
        public string $email
    ) {
    }

    /**
     * Khởi tạo DTO từ Form Request
     */
    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            email: $request->validated('email'),
        );
    }
}
