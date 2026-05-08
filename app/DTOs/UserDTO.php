<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\UserRequest;

readonly class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password,
        public string $role,
    ) {}

    public static function fromRequest(UserRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'] ?? null,
            role: $validated['role'] ?? 'user',
        );
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if ($this->password !== null) {
            $data['password'] = $this->password;
        }

        return $data;
    }
}
