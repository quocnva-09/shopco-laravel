<?php

namespace App\Contracts\Services;

use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;

interface AuthServiceInterface
{
    public function register(RegisterDTO $dto);

    public function login(LoginDTO $dto);

    public function logout(): bool;

    public function getMyInfo();
}
