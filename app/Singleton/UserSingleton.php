<?php

namespace App\Singleton;

use App\Models\User;

class UserSingleton
{
    /**
     * @var User
     */
    private static User $user;

    public static function setUser(User $user): void
    {
        self::$user = $user;
    }

    public static function getUser(): User
    {
        return self::$user;
    }
}
