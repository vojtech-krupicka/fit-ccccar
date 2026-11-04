<?php declare(strict_types=1);

namespace App\Model\RentalOffice;

use App\Model\Employee\EmployeeEntity;
use App\Model\BaseEntity;
use App\Model\EntityList;

final class RentalOfficeEntity extends BaseEntity
{
    /**
     * @param EntityList<EmployeeEntity> $employeeList
     */
    public function __construct(
        public int|null $id,
        public int|null $supervisorId,
        public string $name,
        public string $street,
        public string $city,
        public string $zip,
        public string $country,
        public string $phone,
        public string $email,
        public float $latitude,
        public float $longitude,
        public ?EmployeeEntity $supervisor = null,
        public ?EntityList $employeeList = null,
    ) {}
}
