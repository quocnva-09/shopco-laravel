<?php

declare(strict_types=1);

namespace App\DTOs\User;

use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UserRequest;

readonly class UserDTO
{
    public function __construct(
        public ?string $name,
        public ?string $email,
        public ?string $password,
        public ?string $role,
        public ?string $profile_image,
        public ?string $address,
        public ?string $phone,
        public ?string $bio,
    ) {}

    public static function fromRequest(UserRequest|UpdateUserRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            name: $validated['name'] ?? null,
            email: $validated['email'] ?? null,
            password: $validated['password'] ?? null,
            // If it's a create request, set default role, otherwise null
            role: $validated['role'] ?? ($request instanceof UserRequest ? 'user' : null),
            profile_image: $validated['profile_image'] ?? null,
            address: $validated['address'] ?? null,
            phone: $validated['phone'] ?? null,
            bio: $validated['bio'] ?? null
        );
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
            'profile_image' => $this->profile_image,
            'address' => $this->address,
            'phone' => $this->phone,
            'bio' => $this->bio,
        ];

        // Filter out null values
        return array_filter($data, fn($value) => $value !== null);
    }
}
