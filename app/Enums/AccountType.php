<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AccountType: string implements HasLabel, HasColor
{
    case USER        = 'user';
    case ADMIN       = 'admin';
    case SUPER_ADMIN = 'super_admin';

    public function getLabel(): string
    {
        return match ($this) {
            self::USER        => 'User',
            self::ADMIN       => 'Admin',
            self::SUPER_ADMIN => 'Super Admin',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::USER        => 'gray',
            self::ADMIN       => 'warning',
            self::SUPER_ADMIN => 'danger',
        };
    }
}
