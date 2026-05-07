<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserServiceInterface
{
    public function getAllUsers(): Collection;

    public function getUserById(int $id): User;

    public function createUser(UserDTO $dto): User;

    public function updateUser(int $id, UserDTO $dto): User;

    public function deleteUser(int $id): bool;
}
