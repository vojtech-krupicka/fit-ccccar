<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\Catalog;

use App\Model\Vehicle\VehicleAvailability;
use App\Model\SortMode;

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
        public readonly SortBy $sortBy,
        public readonly SortMode $sortMode,
    ) {}
}
