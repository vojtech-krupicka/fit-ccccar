<?php declare(strict_types=1);

namespace App\Model\Customer;

use App\Model\BaseRepository;
use App\Model\EntityList;
use App\Model\PageRequest;
use Nette\Database\Explorer;

/**
 * @extends BaseRepository<CustomerEntity>
 */
class CustomerRepository extends BaseRepository
{
    public function __construct(Explorer $dbconn, CustomerDbMapper $mapper)
    {
        parent::__construct($dbconn, $mapper);
    }

    public function getByEmail(string $email): CustomerEntity|null
    {
        $row = $this->dbconn->table('customer')->where('email', $email)->fetch();
        return $this->mapper->load($row);
    }

    /**
     * @return EntityList<CustomerEntity>
     */
    public function find(CustomerFilter $filter, ?PageRequest $page): EntityList
    {
        $query = $this->dbconn->table('customer');

        $sortDirection = strtoupper($filter->sortDirection->value);
        switch ($filter->sortBy) {
            case CustomerSortBy::Id:
                $query->order('id ' . $sortDirection);
                break;
            case CustomerSortBy::Fullname:
                $query->order('name ' . $sortDirection);
                $query->order('surname ' . $sortDirection);
                break;
            case CustomerSortBy::Email:
                $query->order('email ' . $sortDirection);
                break;
            case CustomerSortBy::Phone:
                $query->order('phone ' . $sortDirection);
                break;
            case CustomerSortBy::IdentityNumber:
                $query->order('identity_number ' . $sortDirection);
                break;
            case CustomerSortBy::Created:
                $query->order('created_at ' . $sortDirection);
                break;
        }

        return $this->fetchList($query, $page);
    }
}
