<?php

namespace App\Singleton;

use App\Models\User;

class UserSingleton
{
    /**
     * @var User
     */
    private static User $user;

    public static function set(User $user): void
    {
        self::$user = $user;
    }

    public static function get(): User
    {
        return self::$user;
    }
}
