<?php declare(strict_types=1);

namespace App\Model\CarRental;

use App\Model\BaseRepository;
use App\Model\EntityList;
use App\Model\PageRequest;
use Nette\Database\Explorer;

/**
 * @extends BaseRepository<CarRentalEntity>
 */
class CarRentalRepository extends BaseRepository
{
    public function __construct(Explorer $dbconn, CarRentalDbMapper $mapper)
    {
        parent::__construct($dbconn, $mapper);
    }

    /**
     * @return EntityList<CarRentalEntity> Array of car rental entities for the specified vehicle ID.
     * @deprecated Use the find method with CarRentalFilter instead.
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
            entities: $this->mapper->loadMany($query->fetchAll()),
            totalCount: $count,
            currentPage: $page,
            pageCount: $pageCount,
        );

        return $entityList;
    }

    /**
     * @return EntityList<CarRentalEntity>
     */
    public function find(CarRentalFilter $filter, ?PageRequest $page = null): EntityList
    {
        $query = $this->dbconn->table('car_rental');

        if ($filter->vehicleId !== null) {
            $query->where('vehicle_id', $filter->vehicleId);

            if ($filter->activeOnly) {
                $query->where("((`start_at` IS NOT NULL AND `end_at` IS NULL) OR (`start_at` IS NULL AND `deleted` = '0'))");
            }
        }

        switch ($filter->sortBy) {
            case CarRentalSortBy::Latest:
                $query->order('reserved_at DESC, start_at DESC, end_at DESC');
                break;
            case CarRentalSortBy::Oldest:
                $query->order('reserved_at ASC, start_at ASC, end_at ASC');
                break;
            default:
                $query->order('id ASC');
                break;
        }

        return $this->fetchList($query, $page);
    }
}
