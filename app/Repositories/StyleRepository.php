<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\StyleRepositoryInterface;
use App\Models\Style;
use Illuminate\Database\Eloquent\Collection;

class StyleRepository implements StyleRepositoryInterface
{
    public function getAll(): Collection
    {
        return Style::all();
    }
}
