<?php declare(strict_types=1);

namespace App\Model\Customer;

use App\Model\Customer\CustomerSortBy;
use App\Model\SortDirection;

class CustomerFilter
{
    public function __construct(
        public readonly CustomerSortBy $sortBy = CustomerSortBy::Id,
        public readonly SortDirection $sortDirection = SortDirection::Ascending,
    ) {}
}
