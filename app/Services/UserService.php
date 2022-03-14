<?php

namespace App\Services;

use App\Models\LoginToken;
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
            $token = $token . hash('sha256', $millisseconds);
        }
        $loginTokenExist = LoginToken::query()
            ->where('token', $token)
            ->exists();

        if($loginTokenExist) {
            return self::getToken();
        }

        return $token;
    }
}
