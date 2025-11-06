<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EmployeePaymentReason: string implements HasLabel, HasColor
{
    case PAYMENT       = 'payment';
    case BONUS         = 'bonus';
    case DEDUCTION     = 'deduction';
    case ADVANCE       = 'advance';
    case OVERTIME      = 'overtime';
    case REIMBURSEMENT = 'reimbursement';
    case OTHER         = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::PAYMENT       => 'Payment',
            self::BONUS         => 'Bonus',
            self::DEDUCTION     => 'Deduction',
            self::ADVANCE       => 'Advance',
            self::OVERTIME      => 'Overtime',
            self::REIMBURSEMENT => 'Reimbursement',
            self::OTHER         => 'Other',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PAYMENT       => 'success',
            self::BONUS         => 'primary',
            self::DEDUCTION     => 'danger',
            self::ADVANCE       => 'warning',
            self::OVERTIME      => 'info',
            self::REIMBURSEMENT => 'secondary',
            self::OTHER         => 'gray',
        };
    }
}
