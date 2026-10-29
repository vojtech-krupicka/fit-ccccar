<?php declare(strict_types=1);

namespace App\Model\Vehicle;

enum VehicleSortBy: string
{
    case Id = 'id';
    case VehicleName = 'vehicle_name';
    case PlateNumber = 'plate_number';
    case Mileage = 'mileage';
    case Year = 'year';
    case CostPerDay = 'cost_per_day';
    case CostPerUnit = 'cost_per_unit';
}
