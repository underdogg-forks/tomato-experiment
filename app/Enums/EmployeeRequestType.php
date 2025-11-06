<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EmployeeRequestType: string implements HasLabel, HasColor
{
    case HOLIDAY     = 'holiday';
    case SICK_LEAVE  = 'sick_leave';
    case PERMISSION  = 'permission';
    case REMOTE_WORK = 'remote_work';
    case OTHER       = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::HOLIDAY     => 'Holiday',
            self::SICK_LEAVE  => 'Sick Leave',
            self::PERMISSION  => 'Permission',
            self::REMOTE_WORK => 'Remote Work',
            self::OTHER       => 'Other',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::HOLIDAY     => 'success',
            self::SICK_LEAVE  => 'warning',
            self::PERMISSION  => 'info',
            self::REMOTE_WORK => 'primary',
            self::OTHER       => 'gray',
        };
    }
}
