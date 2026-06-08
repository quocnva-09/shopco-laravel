<?php

namespace App\DTOs\Auth;

use Illuminate\Foundation\Http\FormRequest;

readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password
    ) {}

    /**
     * Initialise the DTO from a Form Request
     */
    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );
    }
}
