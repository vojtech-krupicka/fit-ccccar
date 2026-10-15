<?php declare(strict_types=1);

namespace App\Model\Customer;

use App\Model\BaseRepository;
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
}
