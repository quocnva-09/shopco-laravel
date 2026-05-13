<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\DTOs\User\UserDTO;
use App\DTOs\User\UserFilterDTO;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService implements UserServiceInterface
{
    public function __construct(
        protected readonly UserRepositoryInterface $repo,
    ) {
    }

    public function getAllUsers(UserFilterDTO $filter): LengthAwarePaginator
    {
        return $this->repo->paginateAll($filter);
    }

    public function getUserById(int $id): User
    {
        return $this->repo->findById($id);
    }

    public function createUser(UserDTO $dto): User
    {
        $userData             = $dto->toArray();
        $userData['password'] = bcrypt($dto->password);

        return $this->repo->create($userData);
    }

    public function updateUser(int $id, UserDTO $dto): User
    {
        $user     = $this->repo->findById($id);
        $userData = $dto->toArray();

        if ($dto->password) {
            $userData['password'] = bcrypt($dto->password);
        }

        return $this->repo->update($user, $userData);
    }

    public function deleteUser(int $id): bool
    {
        $user = $this->repo->findById($id);

        return $this->repo->delete($user);
    }

    public function getTrashed(UserFilterDTO $filter): LengthAwarePaginator
    {
        return $this->repo->paginateTrashed($filter);
    }

    public function restore(int $id): User
    {
        $user = $this->repo->findTrashedById($id);

        return $this->repo->restore($user);
    }

    public function forceDelete(int $id): bool
    {
        $user = $this->repo->findTrashedById($id);

        return $this->repo->forceDelete($user);
    }
}
