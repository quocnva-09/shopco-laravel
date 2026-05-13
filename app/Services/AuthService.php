<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        protected readonly UserRepositoryInterface $userRepo,
    ) {
    }

    public function register(RegisterDTO $dto): array
    {
        $user = $this->userRepo->create([
            'name'     => $dto->name,
            'email'    => $dto->email,
            'password' => bcrypt($dto->password, ['rounds' => 10]),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    public function login(LoginDTO $dto): array
    {
        $user = $this->userRepo->findByEmail($dto->email);

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw new Exception('Invalid credentials');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    public function logout(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return (bool) $user->currentAccessToken()->delete();
    }

    public function logoutAllDevices(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return (bool) $user->tokens()->delete();
    }

    public function getMyInfo(): ?User
    {
        return Auth::user();
    }
}
