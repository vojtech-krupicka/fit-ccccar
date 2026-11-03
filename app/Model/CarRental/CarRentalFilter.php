<?php declare(strict_types=1);

namespace App\Model\CarRental;

use App\Model\SortDirection;

class CarRentalFilter
{
    public function __construct(
        public readonly ?int $vehicleId = null,
        public readonly ?int $customerId = null,
        public readonly ?bool $activeOnly = false,
        public readonly CarRentalSortBy $sortBy = CarRentalSortBy::Latest,
        public readonly SortDirection $sortDirection = SortDirection::Descending,
    ) {}
}
