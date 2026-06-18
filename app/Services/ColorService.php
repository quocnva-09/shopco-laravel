<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\ColorRepositoryInterface;
use App\Contracts\Services\ColorServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class ColorService implements ColorServiceInterface
{
    public function __construct(
        protected readonly ColorRepositoryInterface $repo
    ) {
    }

    public function getAll(): Collection
    {
        return $this->repo->getAll();
    }
}
