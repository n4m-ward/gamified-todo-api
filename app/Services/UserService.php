<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public static function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public static function getToken(): string
    {
        $token = '';
        for ($i = 0; $i <= 10; $i++) {
            $millisseconds = round(microtime(true) * 1000);
            $token = $token . md5($millisseconds);
        }

        return $token;
    }
}
