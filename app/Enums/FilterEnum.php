<?php

namespace App\Enums;

enum FilterEnum
{
    public const CATEGORY_SORT = [
        'created_at',
        'name',
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
