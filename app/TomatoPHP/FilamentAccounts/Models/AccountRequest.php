<?php

namespace TomatoPHP\FilamentAccounts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountRequest extends Model
{
    use HasFactory;

    protected $table = 'account_requests';

    protected $fillable = [
        'account_id',
        'status',
        'notes',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Account::class, 'account_id');
    }
}
