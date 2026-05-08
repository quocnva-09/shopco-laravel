<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\UserDTO;
use App\DTOs\UserFilterDTO;
use App\Models\User;

interface UserServiceInterface
{
    public function getAllUsers(UserFilterDTO $filter);

    public function getUserById(int $id): User;

    public function createUser(UserDTO $dto): User;

    public function updateUser(int $id, UserDTO $dto): User;

    public function deleteUser(int $id): bool;
}
