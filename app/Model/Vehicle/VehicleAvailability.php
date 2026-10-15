<?php declare(strict_types=1);

namespace App\Model\Vehicle;

enum VehicleAvailability: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Rented = 'rented';
}
