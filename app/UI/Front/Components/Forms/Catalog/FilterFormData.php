<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\Catalog;

use App\Model\Vehicle\VehicleAvailability;
use App\Model\Vehicle\VehicleFilter;
use App\Model\Vehicle\VehicleSortBy;
use App\Model\SortDirection;

class FilterFormData
{
    public function __construct(
        public readonly ?int $rentalOffice,
        public readonly ?int $fuelType,
        public readonly ?int $color,
        public readonly ?float $costPerDayFrom,
        public readonly ?float $costPerDayTo,
        public readonly ?float $costPerUnitFrom,
        public readonly ?float $costPerUnitTo,
        public readonly ?VehicleAvailability $availability,
        public readonly VehicleSortBy $sortBy,
        public readonly SortDirection $sortDirection,
    ) {}

    public function toFilter(): VehicleFilter
    {
        return new VehicleFilter(
            rentalOffice: $this->rentalOffice,
            fuelType: $this->fuelType,
            color: $this->color,
            costPerDayFrom: $this->costPerDayFrom,
            costPerDayTo: $this->costPerDayTo,
            costPerUnitFrom: $this->costPerUnitFrom,
            costPerUnitTo: $this->costPerUnitTo,
            availability: $this->availability,
            sortBy: $this->sortBy,
            sortDirection: $this->sortDirection,
        );
    }
}
