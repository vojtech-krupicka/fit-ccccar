<?php declare(strict_types=1);

namespace App\Model\Employee;

use App\Model\BaseEntity;

final class EmployeeEntity extends BaseEntity
{
    public function __construct(
        public int|null $id = null,
        public int|null $rentalOfficeId = null,
        public string $password = '',
        public string $email = '',
        public string $firstname = '',
        public string $surname = '',
        public string $phone = '',
    ) {}
}
