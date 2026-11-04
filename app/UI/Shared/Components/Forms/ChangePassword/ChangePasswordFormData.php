<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Forms\ChangePassword;

class ChangePasswordFormData
{
    public function __construct(
        public readonly string $oldPassword,
        public readonly string $newPassword,
    ) {}
}
