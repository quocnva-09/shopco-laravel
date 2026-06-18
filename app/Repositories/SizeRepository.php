<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\SizeRepositoryInterface;
use App\Models\Size;
use Illuminate\Database\Eloquent\Collection;

class SizeRepository implements SizeRepositoryInterface
{
    public function getAll(): Collection
    {
        return Size::all();
    }
}
