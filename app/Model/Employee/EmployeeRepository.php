<?php declare(strict_types=1);

namespace App\Model\Employee;

use App\Model\BaseRepository;
use Nette\Database\Explorer;

/**
 * @extends BaseRepository<EmployeeEntity>
 */
class EmployeeRepository extends BaseRepository
{
    public function __construct(Explorer $dbconn, EmployeeDbMapper $mapper)
    {
        parent::__construct($dbconn, $mapper);
    }

    public function getByEmail(string $email): EmployeeEntity|null
    {
        $row = $this->dbconn->table('employee')->where('email', $email)->fetch();
        return $this->mapper->load($row);
    }
}
