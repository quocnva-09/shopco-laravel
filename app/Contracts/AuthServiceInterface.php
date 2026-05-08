<?php

namespace App\Contracts;

use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;
use App\Models\User;

interface AuthServiceInterface
{
    public function register(RegisterDTO $dto);

    public function login(LoginDTO $dto);

    public function logout(): bool;

    public function getMyInfo();
}
