<?php

namespace App\DTOs\Auth;

use Illuminate\Foundation\Http\FormRequest;

readonly class RegisterDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $phone = null,
    ) {
    }

    /**
     * Initialise the DTO from a Form Request
     */
    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
            phone: $request->validated('phone'),
        );
    }
}
