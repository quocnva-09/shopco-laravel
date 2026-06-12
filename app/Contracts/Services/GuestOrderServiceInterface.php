<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\DTOs\Order\GuestCheckoutDTO;
use App\Models\Order;

interface GuestOrderServiceInterface
{
    public function checkout(GuestCheckoutDTO $dto): Order;

    public function verifyOtp(int $orderId, string $otp): bool;

    public function resendOtp(int $orderId): bool;
}
