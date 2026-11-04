<?php declare(strict_types=1);

namespace App\Model\RentalOffice;

use App\Model\BaseRepository;
use App\Model\EntityList;
use App\Model\PageRequest;
use Nette\Database\Explorer;

/**
 * @extends BaseRepository<RentalOfficeEntity>
 */
class RentalOfficeRepository extends BaseRepository
{
    public function __construct(Explorer $dbconn, RentalOfficeDbMapper $mapper)
    {
        parent::__construct($dbconn, $mapper);
    }

    /**
     * @return EntityList<RentalOfficeEntity>
     */
    public function find(RentalOfficeFilter $filter, ?PageRequest $page): EntityList
    {
        $query = $this->dbconn->table('rental_office');

        $sortDirection = strtoupper($filter->sortDirection->value);
        switch ($filter->sortBy) {
            case RentalOfficeSortBy::Id:
                $query->order('id ' . $sortDirection);
                break;
            case RentalOfficeSortBy::Name:
                $query->order('name ' . $sortDirection);
                break;
        }

        return $this->fetchList($query, $page);
    }
}
