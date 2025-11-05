<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EmployeePaymentType: string implements HasLabel, HasColor
{
    case IN  = 'in';
    case OUT = 'out';

    public function getLabel(): string
    {
        return match ($this) {
            self::IN  => 'Income',
            self::OUT => 'Expense',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::IN  => 'success',
            self::OUT => 'danger',
        };
    }
}
