<?php declare(strict_types=1);

namespace App\Model\Vehicle;

enum VehicleSortBy: string
{
    case VehicleName = 'vehicle';
    case CostPerDay = 'day_cost';
    case CostPerUnit = 'unit_cost';
}
