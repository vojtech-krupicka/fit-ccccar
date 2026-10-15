<?php declare(strict_types=1);

namespace App\Model\Vehicle\Color;

use App\Model\BaseRepository;

class VehicleColorRepository extends BaseRepository
{
    public function __construct(
        private readonly \Nette\Database\Explorer $dbconn,
        private readonly VehicleColorDbMapper $vehicleColorDbMapper,
    ) {}

    public function get(int $id): VehicleColorEntity|null
    {
        $row = $this->dbconn->table('vehicle_color')->get($id);
        return $this->vehicleColorDbMapper->load($row);
    }

    public function insert(VehicleColorEntity $vehicleColor): void
    {
        $row = $this->dbconn->table('vehicle_color')->insert($this->vehicleColorDbMapper->dump($vehicleColor));
        $vehicleColor->id = (int) $row['id'];
    }

    public function update(VehicleColorEntity $vehicleColor): void
    {
        $row = $this->dbconn->table('vehicle_color')->get($vehicleColor->id);
        if ($row != null) {
            $row->update($this->vehicleColorDbMapper->dump($vehicleColor));
        }
    }

    public function delete(int $id): void
    {
        $row = $this->dbconn->table('vehicle_color')->get($id);
        if ($row != null) {
            $row->delete();
        }
    }

    /**
     * @return list<VehicleColorEntity>
     */
    public function getAll(): array
    {
        $rows = $this->dbconn->table('vehicle_color')->fetchAll();
        return $this->vehicleColorDbMapper->loadMany($rows);
    }
}
