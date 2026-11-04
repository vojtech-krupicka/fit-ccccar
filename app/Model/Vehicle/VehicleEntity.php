<?php declare(strict_types=1);

namespace App\Model\Vehicle;

use App\Model\RentalOffice\RentalOfficeEntity;
use App\Model\Vehicle\Color\VehicleColorEntity;
use App\Model\Vehicle\FuelType\VehicleFuelTypeEntity;
use App\Model\Vehicle\Type\VehicleTypeEntity;
use App\Model\BaseEntity;

final class VehicleEntity extends BaseEntity
{
    public function __construct(
        public int|null $id,
        public int|null $rentalOfficeId,
        public int|null $typeId,
        public int|null $colorId,
        public int|null $fuelTypeId,
        public string $model,
        public string $plateNumber = '',
        public float $mileage = 0.0,
        public int $year = -1,
        public float $costPerDay = 0.0,
        public float $costPerUnit = 0.0,
        public RentalOfficeEntity|null $rentalOffice = null,
        public VehicleTypeEntity|null $type = null,
        public VehicleColorEntity|null $color = null,
        public VehicleFuelTypeEntity|null $fuelType = null,
    ) {}

    public function getFullName(): string
    {
        return $this->type !== null ? "{$this->type->manufacturer} {$this->type->brand} {$this->model}" : $this->model;
    }
}
