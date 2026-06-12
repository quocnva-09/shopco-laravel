<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case NOT_VERIFY = 'not_verify';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';
}
