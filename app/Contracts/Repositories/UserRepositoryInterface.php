<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\DTOs\User\UserFilterDTO;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function paginateAll(UserFilterDTO $filter): LengthAwarePaginator;

    public function findById(int $id): User;

    public function findTrashedById(int $id): User;

    public function findByEmail(string $email): ?User;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): bool;

    public function paginateTrashed(UserFilterDTO $filter): LengthAwarePaginator;

    public function restore(User $user): User;

    public function forceDelete(User $user): bool;
}
