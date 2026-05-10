<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\UserServiceInterface;
use App\DTOs\User\UserDTO;
use App\DTOs\User\UserFilterDTO;
use App\Enums\FilterEnum;
use App\Models\User;

class UserService implements UserServiceInterface
{
    public function getAllUsers(UserFilterDTO $filter)
    {
        $query = User::query();

        if ($filter->search) {
            $query->where('name', 'like', '%'.$filter->search.'%')
                ->orWhere('email', 'like', '%'.$filter->search.'%');
        }

        if (in_array($filter->sortBy, FilterEnum::USER_SORT)) {
            $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION) ? $filter->sortDir : 'desc';
            $query->orderBy($filter->sortBy, $direction);
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
