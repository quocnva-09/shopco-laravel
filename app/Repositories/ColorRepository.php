<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\ColorRepositoryInterface;
use App\Models\Color;
use Illuminate\Database\Eloquent\Collection;

class ColorRepository implements ColorRepositoryInterface
{
    public function getAll(): Collection
    {
        return Color::all();
    }
}
