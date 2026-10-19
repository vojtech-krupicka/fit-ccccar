<?php declare(strict_types=1);

namespace App\Model\CarRental;

class CarRentalFilter
{
    public function __construct(
        public readonly ?int $vehicleId = null,
        public readonly ?bool $activeOnly = false,
        public readonly CarRentalSortBy $sortBy = CarRentalSortBy::Latest,
    ) {}
}
