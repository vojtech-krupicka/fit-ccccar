<?php declare(strict_types=1);

namespace App\UI\Admin\Components\Forms\Profile;

class ProfileFormData
{
    public function __construct(
        public int $rentalOffice,
        public string $email,
        public string $firstName,
        public string $lastName,
        public string $phone,
    ) {}
}
