<?php

namespace App\Validators;

use App\Dto\UserDto;
use App\Models\User;

class UserValidator
{
    public const PARAMS_TO_CREATE_ACCOUNT = [
        'name',
        'email',
        'password',
        'confirmPassword',
    ];

    public const PARAMS_TO_LOGIN = [
        'email',
        'password',
    ];

    public const INVALID_VALUES = ['', ' ', null];

    public const EMAIL_NEEDED_STR = ['@', '.'];

    public static function validateLogin(UserDto $userDto): bool
    {
        foreach (self::PARAMS_TO_LOGIN as $param) {
            $paramValue = $userDto->{$param} ?? null;
            if (in_array($paramValue, self::INVALID_VALUES)) {
                return false;
            }
        }
        $user = User::query()
            ->where('email', $userDto->email)
            ->first();

        if (is_null($user)) {
            return false;
        }

        if(md5($userDto->password) != $user->password) {
            return false;
        }

        return true;
    }

    public static function validateCreateAccount(UserDto $userDto): array
    {
        $returnMessage = [
            'error' => false,
            'message' => 'Usuario cadastrado!',
        ];

        foreach (self::PARAMS_TO_CREATE_ACCOUNT as $param) {
            $value = $userDto->{$param} ?? null;
            if (in_array($value, self::INVALID_VALUES)) {
                $returnMessage['error'] = true;
                $returnMessage['message'] = "Error: Parametro [$param] invalido!";

            }
        }
        foreach (self::EMAIL_NEEDED_STR as $str) {
            if(!str_contains($userDto->email, $str)) {
                $returnMessage['error'] = true;
                $returnMessage['message'] = "Error: Parametro [email] invalido!";
            }
        }

        if ($userDto->password != $userDto->confirmPassword) {
            $returnMessage['error'] = true;
            $returnMessage['message'] = "Error: Senhas não conferem!";
        }

        return $returnMessage;
    }
}
