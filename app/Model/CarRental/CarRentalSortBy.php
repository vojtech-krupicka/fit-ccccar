<?php declare(strict_types=1);

namespace App\Model\CarRental;

enum CarRentalSortBy: string
{
    case Latest = 'latest';  // newest reservations first
    case Oldest = 'oldest';
}
