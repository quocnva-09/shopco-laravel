<?php

namespace App\Services;

use App\Contracts\AuthServiceInterface;
use App\Models\User;
use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;
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
            'token' => $token
        ];
    }

    public function login(LoginDTO $dto)
    {
        $user = User::where('email', $dto->email)->first();

        $token = $user->createToken('auth_token')->plainTextToken;

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function logout(User $user): bool
    {
        try {
            $user->currentAccessToken()->delete();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}