<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService implements UserServiceInterface
{
    public function getAllUsers(): Collection
    {
        return User::all();
    }

    public function getUserById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function createUser(UserDTO $dto): User
    {
        $userData = $dto->toArray();
        $userData['password'] = bcrypt($dto->password);
        return User::create($userData);
    }

    public function updateUser(int $id, UserDTO $dto): User
    {
        $user = $this->getUserById($id);
        $userData = $dto->toArray();
        if ($dto->password) {
            $userData['password'] = bcrypt($dto->password);
        }
        $user->update($userData);

        return $user;
    }

    public function deleteUser(int $id): bool
    {
        $user = $this->getUserById($id);

        return $user->delete();
    }
}
