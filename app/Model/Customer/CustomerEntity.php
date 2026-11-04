<?php declare(strict_types=1);

namespace App\Model\Customer;

use App\Model\BaseEntity;

final class CustomerEntity extends BaseEntity
{
    public readonly string $fullname;

    public function __construct(
        public int|null $id,
        public string $password,
        public string $email = '',
        public string $firstname = '',
        public string $surname = '',
        public string $identityNumber = '',
        public string $phone = '',
        public string $street = '',
        public string $city = '',
        public string $zip = '',
        public string $country = '',
        public \DateTime $created = new \DateTime(),
    ) {
        $this->fullname = $firstname . ' ' . $surname;
    }
}
