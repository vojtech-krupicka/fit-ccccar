<?php declare(strict_types=1);

namespace App\Model\Vehicle\Color;

use App\Model\BaseEntity;

final class VehicleColorEntity extends BaseEntity
{
    public function __construct(
        public int $id,
        public string $name,
        public string $hex
    ) {}
}
