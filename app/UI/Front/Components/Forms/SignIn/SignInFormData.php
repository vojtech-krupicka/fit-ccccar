<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\SignIn;

class SignInFormData
{
    public readonly string $username;
    public readonly string $password;
    public readonly bool $persistent;
}
