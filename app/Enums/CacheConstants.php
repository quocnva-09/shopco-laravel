<?php

declare(strict_types=1);

namespace App\Enums;

enum CacheConstants: string
{
    case PRODUCT_TAGS = 'products';
    case CATEGORY_TAGS = 'categories';

    public const CACHE_TTL = 3600;
}
