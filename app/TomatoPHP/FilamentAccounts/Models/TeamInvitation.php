<?php

namespace TomatoPHP\FilamentAccounts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamInvitation extends Model
{
    use HasFactory;

    protected $table = 'team_invitations';

    protected $fillable = [
        'team_id',
        'email',
        'token',
        'status',
    ];
}
