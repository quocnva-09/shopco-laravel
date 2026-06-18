<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\SizeRepositoryInterface;
use App\Contracts\Services\SizeServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class SizeService implements SizeServiceInterface
{
    public function __construct(
        protected readonly SizeRepositoryInterface $repo
    ) {
    }

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }
}
