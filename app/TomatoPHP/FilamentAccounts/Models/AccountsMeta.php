<?php

namespace TomatoPHP\FilamentAccounts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountsMeta extends Model
{
    use HasFactory;

    protected $table = 'accounts_meta';

    protected $fillable = [
        'account_id',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];
}
