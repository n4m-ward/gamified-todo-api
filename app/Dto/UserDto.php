<?php

namespace App\Dto;

class UserDto extends Dto
{
    public string $name;
    public string $email;
    public string $password;
    public string $confirmPassword;
}
