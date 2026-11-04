<?php declare(strict_types=1);

namespace App\Model\RentalOffice;

use App\Model\RentalOffice\RentalOfficeSortBy;
use App\Model\SortDirection;

class RentalOfficeFilter
{
    public function __construct(
        public readonly RentalOfficeSortBy $sortBy = RentalOfficeSortBy::Id,
        public readonly SortDirection $sortDirection = SortDirection::Ascending,
    ) {}
}
