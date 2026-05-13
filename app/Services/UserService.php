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
        $query = User::query()->select(['id', 'name', 'email', 'role', 'created_at']);

        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%'.$filter->search.'%')
                  ->orWhere('email', 'like', '%'.$filter->search.'%');
            });
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
        $user = User::with('cart')->findOrFail($id);

        return $user->delete();
    }

    public function getTrashed(UserFilterDTO $filter)
    {
        $query = User::onlyTrashed()->select(['id', 'name', 'email', 'role', 'deleted_at']);

        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%'.$filter->search.'%')
                  ->orWhere('email', 'like', '%'.$filter->search.'%');
            });
        }

        if (in_array($filter->sortBy, FilterEnum::USER_SORT)) {
            $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION) ? $filter->sortDir : 'desc';
            $query->orderBy($filter->sortBy, $direction);
        } else {
            $query->orderBy('deleted_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    public function restore(int $id): User
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return $user;
    }

    public function forceDelete(int $id): bool
    {
        $user = User::withTrashed()->findOrFail($id);

        return $user->forceDelete();
    }
}
