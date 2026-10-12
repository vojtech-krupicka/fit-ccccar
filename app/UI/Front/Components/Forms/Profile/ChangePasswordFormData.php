<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\Profile;

class ChangePasswordFormData
{
    public function __construct(
        public readonly string $oldPassword,
        public readonly string $newPassword,
    ) {}
}
