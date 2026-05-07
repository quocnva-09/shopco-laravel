<?php

namespace App\Contracts;

use App\Models\User;
use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;

interface AuthServiceInterface
{
    public function register(RegisterDTO $dto);

    public function login(LoginDTO $dto);

    public function logout(User $user): bool;

    public function getMyInfo();
}