<?php

namespace App\Dto;

class UserDto extends Dto
{
    public ?string $name = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $confirmPassword = null;
}
