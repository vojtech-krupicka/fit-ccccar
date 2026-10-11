<?php declare(strict_types=1);

namespace App\UI\Admin\Components\Forms\SignIn;

class SignInFormData
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
    ) {}
}
