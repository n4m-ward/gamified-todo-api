<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginToken extends Model
{
    protected $table = 'login_token';

    protected $fillable = [
        'user_id',
        'token',
    ];
}
