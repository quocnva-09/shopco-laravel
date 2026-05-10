<?php

namespace App\Contracts\Services;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;

interface AuthServiceInterface
{
    public function register(RegisterDTO $dto);

    public function login(LoginDTO $dto);

    public function logout(): bool;

    public function getMyInfo();
}
