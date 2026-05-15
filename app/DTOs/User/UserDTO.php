<?php

declare(strict_types=1);

namespace App\DTOs\User;

use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UserRequest;

readonly class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password,
        public string $role,
        public ?string $avatar,
        public ?string $address,
        public ?string $phone_number,
    ) {}

    public static function fromRequest(UserRequest|UpdateUserRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'] ?? null,
            role: $validated['role'] ?? 'user',
            avatar: $validated['avatar'] ?? null,
            address: $validated['address'] ?? null,
            phone_number: $validated['phone_number'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'avatar' => $this->avatar,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
        ];

        if ($this->password !== null) {
            $data['password'] = $this->password;
        }

        return $data;
    }
}
