<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\Catalog;

enum SortBy: string
{
    case VehicleName = 'vehicle';
    case CostPerDay = 'day_cost';
    case CostPerUnit = 'unit_cost';
}
