<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\DTOs\UserDTO;
use App\DTOs\UserFilterDTO;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService implements UserServiceInterface
{
    public function getAllUsers(UserFilterDTO $filter)
    {
        $query = User::query();

        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter->search . '%')
                    ->orWhere('email', 'like', '%' . $filter->search . '%');
            });
        }

        if (in_array($filter->sort, ['created_at', 'name', 'email'])) {
            $direction = in_array(strtolower($filter->direction), ['asc', 'desc']) ? $filter->direction : 'desc';
            $query->orderBy($filter->sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
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
