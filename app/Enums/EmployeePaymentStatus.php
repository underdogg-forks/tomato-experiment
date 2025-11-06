<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EmployeePaymentStatus: string implements HasLabel, HasColor
{
    case PENDING  = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case PAID     = 'paid';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING  => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::PAID     => 'Paid',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING  => 'warning',
            self::APPROVED => 'info',
            self::REJECTED => 'danger',
            self::PAID     => 'success',
        };
    }
}
