<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Models\User;

interface AuthServiceInterface
{
    public function register(RegisterDTO $dto): array;

    public function login(LoginDTO $dto): array;

    public function logout(): bool;

    public function logoutAllDevices(): bool;

    public function getMyInfo(): ?User;
}
