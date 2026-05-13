<?php

namespace App\Services;

use App\Contracts\Services\AuthServiceInterface;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function register(RegisterDTO $dto)
    {
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => bcrypt($dto->password, ['rounds' => 10]),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(LoginDTO $dto)
    {
        $user = User::where('email', $dto->email)->first();

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(): bool
    {
        $user = Auth::user();

        return $user->currentAccessToken()->delete();
    }
    public function logoutAllDevices(): bool
    {
        $user = Auth::user();

        return (bool) $user->tokens()->delete();
    }

    public function getMyInfo()
    {
        $user = Auth::user();

        return $user;
    }
}
