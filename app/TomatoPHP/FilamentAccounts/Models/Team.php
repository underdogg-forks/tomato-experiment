<?php

namespace TomatoPHP\FilamentAccounts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';

    protected $fillable = [
        'name',
        'owner_id',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Account::class, 'team_memberships', 'team_id', 'account_id')
            ->withPivot(['role']);
    }
}
