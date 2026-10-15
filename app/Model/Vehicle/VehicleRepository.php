<?php declare(strict_types=1);

namespace App\Model\Vehicle;

use App\Model\Vehicle\VehicleAvailability;
use App\Model\BaseRepository;
use App\Model\EntityList;
use App\Model\SortMode;
use App\UI\Front\Components\Forms\Catalog\SortBy;

class VehicleRepository extends BaseRepository
{
    public function __construct(
        private readonly \Nette\Database\Explorer $dbconn,
        private readonly VehicleDbMapper $vehicleDbMapper,
    ) {}

    public function get(int $id): VehicleEntity|null
    {
        $row = $this->dbconn->table('vehicle')->get($id);
        return $this->vehicleDbMapper->load($row);
    }

    public function insert(VehicleEntity $vehicle): void
    {
        $row = $this->dbconn->table('vehicle')->insert($this->vehicleDbMapper->dump($vehicle));
        $vehicle->id = (int) $row['id'];
    }

    public function update(VehicleEntity $vehicle): void
    {
        $row = $this->dbconn->table('vehicle')->get($vehicle->id);
        if ($row != null) {
            $row->update($this->vehicleDbMapper->dump($vehicle));
        }
    }

    public function delete(int $id): void
    {
        $row = $this->dbconn->table('vehicle')->get($id);
        if ($row != null) {
            $row->delete();
        }
    }

    public function getCount(): int
    {
        return $this->dbconn->table('vehicle')->count();
    }

    /**
     * @return list<VehicleEntity>
     */
    public function getAll(): array
    {
        $rows = $this->dbconn->table('vehicle')->fetchAll();
        return $this->vehicleDbMapper->loadMany($rows);
    }

    /**
     * @return EntityList<VehicleEntity>
     */
    public function find(
        ?int $rentalOffice = null,
        ?int $fuelType = null,
        ?int $color = null,
        ?float $costPerDayFrom = null,
        ?float $costPerDayTo = null,
        ?float $costPerUnitFrom = null,
        ?float $costPerUnitTo = null,
        ?VehicleAvailability $availability = null,
        SortBy $sortBy = SortBy::VehicleName,
        SortMode $sortMode = SortMode::Ascending,
        int $page = 1,
        int $itemsPerPage = 100,
    ): EntityList {
        $query = $this->dbconn->table('vehicle');

        if ($rentalOffice !== null) {
            $query->where('rental_office_id', $rentalOffice);
        }
        if ($fuelType !== null) {
            $query->where('vehicle_fuel_type_id', $fuelType);
        }
        if ($color !== null) {
            $query->where('vehicle_color_id', $color);
        }
        if ($costPerDayFrom !== null) {
            $query->where('cost_per_day >= ?', $costPerDayFrom);
        }
        if ($costPerDayTo !== null) {
            $query->where('cost_per_day <= ?', $costPerDayTo);
        }
        if ($costPerUnitFrom !== null) {
            $query->where('cost_per_unit >= ?', $costPerUnitFrom);
        }
        if ($costPerUnitTo !== null) {
            $query->where('cost_per_unit <= ?', $costPerUnitTo);
        }

        switch ($sortBy) {
            case SortBy::CostPerDay:
                $query->order('cost_per_day ' . $sortMode->value);
                break;
            case SortBy::CostPerUnit:
                $query->order('cost_per_unit ' . $sortMode->value);
                break;
            default:
                $query->order('vehicle_type.manufacturer ' . strtoupper($sortMode->value));
                $query->order('vehicle_type.brand ' . strtoupper($sortMode->value));
                break;
        }

        $pageCount = 0;
        $count = $query->count();
        $query->page($page, $itemsPerPage, $pageCount);

        $entityList = new EntityList(
            entities: $this->vehicleDbMapper->loadMany($query->fetchAll()),
            totalCount: $count,
            currentPage: $page,
            pageCount: $pageCount,
        );

        return $entityList;
    }
}
