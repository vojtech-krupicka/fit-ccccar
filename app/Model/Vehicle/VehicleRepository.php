<?php declare(strict_types=1);

namespace App\Model\Vehicle;

use App\Model\Vehicle\VehicleDbMapper;
use App\Model\Vehicle\VehicleEntity;
use App\Model\Vehicle\VehicleFilter;
use App\Model\Vehicle\VehicleSortBy;
use App\Model\BaseRepository;
use App\Model\EntityList;
use App\Model\PageRequest;
use Nette\Database\Explorer;

/**
 * @extends BaseRepository<VehicleEntity>
 */
class VehicleRepository extends BaseRepository
{
    public function __construct(Explorer $dbconn, VehicleDbMapper $mapper)
    {
        parent::__construct($dbconn, $mapper);
    }

    /**
     * @return EntityList<VehicleEntity>
     */
    public function find(VehicleFilter $filter, ?PageRequest $page): EntityList
    {
        $query = $this->dbconn->table('vehicle');

        if ($filter->rentalOffice !== null) {
            $query->where('rental_office_id', $filter->rentalOffice);
        }
        if ($filter->fuelType !== null) {
            $query->where('vehicle_fuel_type_id', $filter->fuelType);
        }
        if ($filter->color !== null) {
            $query->where('vehicle_color_id', $filter->color);
        }
        if ($filter->costPerDayFrom !== null) {
            $query->where('cost_per_day >= ?', $filter->costPerDayFrom);
        }
        if ($filter->costPerDayTo !== null) {
            $query->where('cost_per_day <= ?', $filter->costPerDayTo);
        }
        if ($filter->costPerUnitFrom !== null) {
            $query->where('cost_per_unit >= ?', $filter->costPerUnitFrom);
        }
        if ($filter->costPerUnitTo !== null) {
            $query->where('cost_per_unit <= ?', $filter->costPerUnitTo);
        }

        switch ($filter->sortBy) {
            case VehicleSortBy::CostPerDay:
                $query->order('cost_per_day ' . $filter->sortDirection->value);
                break;
            case VehicleSortBy::CostPerUnit:
                $query->order('cost_per_unit ' . $filter->sortDirection->value);
                break;
            default:
                $query->order('vehicle_type.manufacturer ' . $filter->sortDirection->value);
                $query->order('vehicle_type.brand ' . $filter->sortDirection->value);
                break;
        }

        return $this->fetchList($query, $page);
    }
}
