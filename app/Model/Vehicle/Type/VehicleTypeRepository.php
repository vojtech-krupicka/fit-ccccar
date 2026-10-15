<?php declare(strict_types=1);

namespace App\Model\Vehicle\Type;

use App\Model\BaseRepository;

class VehicleTypeRepository extends BaseRepository
{
    public function __construct(
        private readonly \Nette\Database\Explorer $dbconn,
        private readonly VehicleTypeDbMapper $vehicleTypeDbMapper,
    ) {}

    public function get(int $id): VehicleTypeEntity|null
    {
        $row = $this->dbconn->table('vehicle_type')->get($id);
        return $this->vehicleTypeDbMapper->load($row);
    }

    public function insert(VehicleTypeEntity $vehicleType): void
    {
        $row = $this->dbconn->table('vehicle_type')->insert($this->vehicleTypeDbMapper->dump($vehicleType));
        $vehicleType->id = (int) $row['id'];
    }

    public function update(VehicleTypeEntity $vehicleType): void
    {
        $row = $this->dbconn->table('vehicle_type')->get($vehicleType->id);
        if ($row != null) {
            $row->update($this->vehicleTypeDbMapper->dump($vehicleType));
        }
    }

    public function delete(int $id): void
    {
        $row = $this->dbconn->table('vehicle_type')->get($id);
        if ($row != null) {
            $row->delete();
        }
    }

    /**
     * @return list<VehicleTypeEntity>
     */
    public function getAll(): array
    {
        $rows = $this->dbconn->table('vehicle_type')->fetchAll();
        return $this->vehicleTypeDbMapper->loadMany($rows);
    }
}
