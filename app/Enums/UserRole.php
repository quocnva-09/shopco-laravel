<?php

namespace App\Enums;

enum UserRole: string
{
    case ROLE_ADMIN = 'admin';
    case ROLE_USER = 'user';

    public function isAdmin(): bool
    {
        return $this === self::ROLE_ADMIN;
    }

    public function isUser(): bool
    {
        return $this === self::ROLE_USER;
    }
}
