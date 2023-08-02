<?php

namespace App\Entities;

enum UserRole: string
{
    case USER = "USER";
    case SCHOOL_ADMIN = "SCHOOL_ADMIN";
    case REGION_ADMIN = "REGION_ADMIN";
    case GLOBAL_ADMIN = "GLOBAL_ADMIN";

    public function displayName(): string
    {
        return match ($this) {
            self::USER => 'Benutzer',
            self::SCHOOL_ADMIN => 'Schuladministrator',
            self::REGION_ADMIN => 'Regionaler Administrator',
            self::GLOBAL_ADMIN => 'Globaler Administrator'
        };
    }

    public function isAdmin(): bool
    {
        return match ($this) {
            self::SCHOOL_ADMIN, self::REGION_ADMIN, self::GLOBAL_ADMIN => true,
            self::USER => false
        };
    }
}