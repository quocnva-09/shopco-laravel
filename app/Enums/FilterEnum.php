<?php

namespace App\Enums;

enum FilterEnum
{
    public const CATEGORY_SORT = [
        'created_at',
        'name',
    ];

    public const PRODUCT_SORT = [
        'price',
        'created_at',
        'name',
    ];

    public const USER_SORT = [
        'created_at',
        'name',
        'email',
    ];

    public const ORDER_SORT = [
        'created_at',
        'id',
        'totalAmount',
    ];

    public const DIRECTION = [
        'asc',
        'desc',
    ];

    public static function getString(array $array): string
    {
        return implode(',', $array);
    }
}
