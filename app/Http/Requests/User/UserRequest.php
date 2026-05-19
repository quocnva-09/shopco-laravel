<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UserRequest",
    title: "User Request",
    description: "User creation payload",
    required: ["name", "email", "password"],
    properties: [
        new OA\Property(property: "name", type: "string", example: "John Doe", nullable: false),
        new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com", nullable: false),
        new OA\Property(property: "role", type: "string", example: "user", enum: ["admin", "user"], nullable: true),
        new OA\Property(property: "password", type: "string", format: "password", example: "password", nullable: false),
        new OA\Property(property: "profile_image", type: "string", example: "users/avatar.jpg", nullable: true),
        new OA\Property(property: "address", type: "string", example: "123 Main St", nullable: true),
        new OA\Property(property: "phone", type: "string", example: "1234567890", nullable: true),
        new OA\Property(property: "bio", type: "string", example: "I am a user", nullable: true)
    ]
)]
class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['nullable', 'string', 'in:' . implode(',', UserRole::getValues())],
            'password' => ['required', 'string', 'min:8'],
            'profile_image' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
        ];
    }
}
