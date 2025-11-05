<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AttendanceSource: string implements HasLabel, HasColor
{
    case FINGERPRINT = 'fingerprint';
    case MANUAL      = 'manual';
    case APP         = 'app';
    case WEB         = 'web';

    public function getLabel(): string
    {
        return match ($this) {
            self::FINGERPRINT => 'Fingerprint',
            self::MANUAL      => 'Manual',
            self::APP         => 'Mobile App',
            self::WEB         => 'Web',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::FINGERPRINT => 'success',
            self::MANUAL      => 'warning',
            self::APP         => 'info',
            self::WEB         => 'primary',
        };
    }
}
