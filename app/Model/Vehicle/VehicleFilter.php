<?php declare(strict_types=1);

namespace App\Model\Vehicle;

use App\Model\Vehicle\VehicleAvailability;
use App\Model\Vehicle\VehicleSortBy;
use App\Model\SortDirection;

class VehicleFilter
{
    public function __construct(
        public readonly ?int $rentalOffice = null,
        public readonly ?int $fuelType = null,
        public readonly ?int $color = null,
        public readonly ?float $costPerDayFrom = null,
        public readonly ?float $costPerDayTo = null,
        public readonly ?float $costPerUnitFrom = null,
        public readonly ?float $costPerUnitTo = null,
        public readonly ?VehicleAvailability $availability = null,
        public readonly VehicleSortBy $sortBy = VehicleSortBy::VehicleName,
        public readonly SortDirection $sortDirection = SortDirection::Ascending,
    ) {}
}
