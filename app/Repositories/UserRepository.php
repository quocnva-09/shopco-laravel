<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\DTOs\User\UserFilterDTO;
use App\Enums\FilterEnum;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function paginateAll(UserFilterDTO $filter): LengthAwarePaginator
    {
        $query = User::query()->select(['id', 'name', 'email', 'role', 'created_at']);

        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter->search . '%')
                  ->orWhere('email', 'like', '%' . $filter->search . '%');
            });
        }

        if (in_array($filter->sortBy, FilterEnum::USER_SORT)) {
            $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION)
                ? $filter->sortDir
                : 'desc';
            $query->orderBy($filter->sortBy, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    public function findById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function findTrashedById(int $id): User
    {
        return User::onlyTrashed()->findOrFail($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }

    public function paginateTrashed(UserFilterDTO $filter): LengthAwarePaginator
    {
        $query = User::onlyTrashed()->select(['id', 'name', 'email', 'role', 'deleted_at']);

        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter->search . '%')
                  ->orWhere('email', 'like', '%' . $filter->search . '%');
            });
        }

        if (in_array($filter->sortBy, FilterEnum::USER_SORT)) {
            $direction = in_array(strtolower($filter->sortDir), FilterEnum::DIRECTION)
                ? $filter->sortDir
                : 'desc';
            $query->orderBy($filter->sortBy, $direction);
        } else {
            $query->orderBy('deleted_at', 'desc');
        }

        return $query->paginate($filter->perPage, ['*'], 'page', $filter->page);
    }

    public function restore(User $user): User
    {
        $user->restore();

        return $user;
    }

    public function forceDelete(User $user): bool
    {
        return (bool) $user->forceDelete();
    }
}
