<?php declare(strict_types=1);

namespace App\Model\Vehicle\Type;

use App\Model\BaseEntity;

final class VehicleTypeEntity extends BaseEntity
{
    public function __construct(
        public int|null $id,
        public string $manufacturer,
        public string $brand
    ) {}
}
