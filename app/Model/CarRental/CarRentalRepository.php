<?php declare(strict_types=1);

namespace App\Model\CarRental;

use App\Model\BaseRepository;
use App\Model\EntityList;

class CarRentalRepository extends BaseRepository
{
    public function __construct(
        private readonly \Nette\Database\Explorer $dbconn,
        private readonly CarRentalDbMapper $carRentalDbMapper,
    ) {}

    public function get(int $id): CarRentalEntity|null
    {
        $row = $this->dbconn->table('car_rental')->get($id);
        return $this->carRentalDbMapper->load($row);
    }

    public function insert(CarRentalEntity $carRental): void
    {
        $row = $this->dbconn->table('car_rental')->insert($this->carRentalDbMapper->dump($carRental));
        $carRental->id = (int) $row['id'];
    }

    public function update(CarRentalEntity $carRental): void
    {
        $row = $this->dbconn->table('car_rental')->get($carRental->id);
        if ($row != null) {
            $row->update($this->carRentalDbMapper->dump($carRental));
        }
    }

    public function delete(int $id): void
    {
        $row = $this->dbconn->table('car_rental')->get($id);
        if ($row != null) {
            $row->delete();
        }
    }

    /**
     * @return list<CarRentalEntity> Array of all car rental entities.
     */
    public function getAll(): array
    {
        $rows = $this->dbconn->table('car_rental')->fetchAll();
        return $this->carRentalDbMapper->loadMany($rows);
    }

    /**
     * @return EntityList<CarRentalEntity> Array of car rental entities for the specified vehicle ID.
     */
    public function getByVehicleId(int $vehicleId, int $page = 1, int $itemsPerPage = 10): EntityList
    {
        $query = $this->dbconn->table('car_rental');
        $query->where('vehicle_id', $vehicleId);
        $query->where("((`start_at` IS NOT NULL AND `end_at` IS NULL) OR (`start_at` IS NULL AND `deleted` = '0'))");

        $query->order('reserved_at DESC');
        $query->order('start_at DESC');
        $query->order('end_at DESC');

        $pageCount = 0;
        $count = $query->count();
        $query->page($page, $itemsPerPage, $pageCount);

        $entityList = new EntityList(
            entities: $this->carRentalDbMapper->loadMany($query->fetchAll()),
            totalCount: $count,
            currentPage: $page,
            pageCount: $pageCount,
        );

        return $entityList;
    }
}
