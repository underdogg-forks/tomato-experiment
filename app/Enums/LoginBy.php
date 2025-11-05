<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LoginBy: string implements HasLabel, HasColor
{
    case EMAIL    = 'email';
    case USERNAME = 'username';
    case PHONE    = 'phone';

    public function getLabel(): string
    {
        return match ($this) {
            self::EMAIL    => 'Email',
            self::USERNAME => 'Username',
            self::PHONE    => 'Phone',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::EMAIL    => 'info',
            self::USERNAME => 'success',
            self::PHONE    => 'warning',
        };
    }
}
