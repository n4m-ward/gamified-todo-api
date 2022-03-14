<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoginToken extends Model
{
    use SoftDeletes;

    protected $table = 'login_token';
    protected $fillable = [
        'user_id',
        'token',
    ];
}
