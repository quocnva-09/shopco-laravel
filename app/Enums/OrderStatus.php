<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case NOT_VERIFY = 'not_verify';
    case PROCESSING = 'processing';
    case SHIPPING = 'shipping';
    case DELIVERED = 'delivered';
    case PAID = 'paid';
    case COMPLETE = 'completed';
    case CANCELLED = 'cancelled';
}
