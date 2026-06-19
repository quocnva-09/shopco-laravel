<?php

declare(strict_types=1);

namespace App\Enums;

enum ReviewStatus: string
{
    case APPROVED = 'approved';
    case PENDING = 'pending';
}
