<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\StyleRepositoryInterface;
use App\Contracts\Services\StyleServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class StyleService implements StyleServiceInterface
{
    public function __construct(
        protected readonly StyleRepositoryInterface $repo
    ) {
    }

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }
}
