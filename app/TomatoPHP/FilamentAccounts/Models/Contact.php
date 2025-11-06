<?php

namespace TomatoPHP\FilamentAccounts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contacts';

    protected $fillable = [
        'account_id',
        'name',
        'email',
        'phone',
        'notes',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Account::class, 'account_id');
    }
}
