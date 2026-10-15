<?php declare(strict_types=1);

namespace App\Model\Vehicle\FuelType;

use App\Model\BaseRepository;

class VehicleFuelTypeRepository extends BaseRepository
{
    public function __construct(
        private readonly \Nette\Database\Explorer $dbconn,
        private readonly VehicleFuelTypeDbMapper $vehicleFuelTypeDbMapper,
    ) {}

    public function get(int $id): VehicleFuelTypeEntity|null
    {
        $row = $this->dbconn->table('vehicle_fuel_type')->get($id);
        return $this->vehicleFuelTypeDbMapper->load($row);
    }

    public function insert(VehicleFuelTypeEntity $vehicleFuelType): void
    {
        $row = $this->dbconn->table('vehicle_fuel_type')->insert($this->vehicleFuelTypeDbMapper->dump($vehicleFuelType));
        $vehicleFuelType->id = (int) $row['id'];
    }

    public function update(VehicleFuelTypeEntity $vehicleFuelType): void
    {
        $row = $this->dbconn->table('vehicle_fuel_type')->get($vehicleFuelType->id);
        if ($row != null) {
            $row->update($this->vehicleFuelTypeDbMapper->dump($vehicleFuelType));
        }
    }

    public function delete(int $id): void
    {
        $row = $this->dbconn->table('vehicle_fuel_type')->get($id);
        if ($row != null) {
            $row->delete();
        }
    }

    /**
     * @return list<VehicleFuelTypeEntity>
     */
    public function getAll(): array
    {
        $rows = $this->dbconn->table('vehicle_fuel_type')->fetchAll();
        return $this->vehicleFuelTypeDbMapper->loadMany($rows);
    }
}
