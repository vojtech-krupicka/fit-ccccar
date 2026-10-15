<?php declare(strict_types=1);

namespace App\Model\Vehicle\FuelType;

use App\Model\BaseEntity;

final class VehicleFuelTypeEntity extends BaseEntity
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
