<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\Profile;

class ProfileFormData
{
    public function __construct(
        public readonly string $email,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $idNumber,
        public readonly string $phone,
        public readonly string $street,
        public readonly string $city,
        public readonly string $zip,
        public readonly string $country,
    ) {}
}
