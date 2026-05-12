<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\User\UserDTO;
use App\DTOs\User\UserFilterDTO;
use App\Models\User;

interface UserServiceInterface
{
    public function getAllUsers(UserFilterDTO $filter);

    public function getUserById(int $id): User;

    public function createUser(UserDTO $dto): User;

    public function updateUser(int $id, UserDTO $dto): User;

    public function deleteUser(int $id): bool;

    public function getTrashed(UserFilterDTO $filter);

    public function restore(int $id): User;

    public function forceDelete(int $id): bool;
}
