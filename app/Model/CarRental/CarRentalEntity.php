<?php declare(strict_types=1);

namespace App\Model\CarRental;

use App\Model\Customer\CustomerEntity;
use App\Model\Employee\EmployeeEntity;
use App\Model\RentalOffice\RentalOfficeEntity;
use App\Model\Vehicle\VehicleEntity;
use App\Model\BaseEntity;

final class CarRentalEntity extends BaseEntity
{
    public function __construct(
        public int|null $id,
        public int $vehicleId,
        public int $rentalOfficeId,
        public int $customerId,
        public int|null $borrowerId,
        public int|null $recipientId,
        public \DateTime $reservedAt,
        public \DateTime $reservedFrom,
        public \DateTime $reservedTo,
        public \DateTime|null $startAt = null,
        public \DateTime|null $endAt = null,
        public float $distance = 0.0,
        public float $price = 0.0,
        public bool $deleted = false,
        public ?VehicleEntity $vehicle = null,
        public ?RentalOfficeEntity $rentalOffice = null,
        public ?CustomerEntity $customer = null,
        public ?EmployeeEntity $borrower = null,
        public ?EmployeeEntity $recipient = null,
    ) {}
}
