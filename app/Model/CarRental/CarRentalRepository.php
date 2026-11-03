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
        }
        if ($filter->activeOnly) {
            $query->where("((`start_at` IS NOT NULL AND `end_at` IS NULL) OR (`start_at` IS NULL AND `deleted` = '0'))");
        }

        $sortDirection = $filter->sortDirection->toSql();
        switch ($filter->sortBy) {
            case CarRentalSortBy::Latest:
                $query->order('reserved_at DESC, start_at DESC, end_at DESC');
                break;
            case CarRentalSortBy::Oldest:
                $query->order('reserved_at ASC, start_at ASC, end_at ASC');
                break;
            case CarRentalSortBy::Vehicle:
                $query->order('vehicle.vehicle_type.manufacturer ' . $sortDirection);
                $query->order('vehicle.vehicle_type.brand ' . $sortDirection);
                break;
            case CarRentalSortBy::RentalOffice:
                $query->order('rental_office.name ' . $sortDirection);
                break;
            case CarRentalSortBy::Customer:
                $query->order('customer.name ' . $sortDirection);
                $query->order('customer.surname ' . $sortDirection);
                break;
            case CarRentalSortBy::Borrower:
                $query->order('borrower.name ' . $sortDirection);
                $query->order('borrower.surname ' . $sortDirection);
                break;
            case CarRentalSortBy::Recipient:
                $query->order('recipient.name ' . $sortDirection);
                $query->order('recipient.surname ' . $sortDirection);
                break;
            case CarRentalSortBy::Distance:
                $query->order('distance ' . $sortDirection);
                break;
            case CarRentalSortBy::Price:
                $query->order('price ' . $sortDirection);
                break;
            case CarRentalSortBy::ReservedAt:
                $query->order('reserved_at ' . $sortDirection);
                break;
            case CarRentalSortBy::StartAt:
                $query->order('start_at ' . $sortDirection);
                break;
            case CarRentalSortBy::Deleted:
                $query->order('deleted ' . $sortDirection);
                break;
            default:
                $query->order('id ' . $sortDirection);
                break;
        }

        return $this->fetchList($query, $page);
    }
}
