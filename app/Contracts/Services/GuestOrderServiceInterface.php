<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Order\GuestCheckoutDTO;
use App\Models\Order;

interface GuestOrderServiceInterface
{
    public function checkout(GuestCheckoutDTO $dto): Order;
}
