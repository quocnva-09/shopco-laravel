<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;

interface SizeServiceInterface
{
    public function getAll(): Collection;
}
