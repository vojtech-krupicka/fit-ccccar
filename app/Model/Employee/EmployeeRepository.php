<?php declare(strict_types=1);

namespace App\Model\Employee;

use App\Model\BaseRepository;

class EmployeeRepository extends BaseRepository
{
    public function get(int $id): EmployeeEntity|null
    {
        $row = $this->dbconn->table('employee')->get($id);
        return EmployeeDbMapper::load($row);
    }

    public function getByEmail(string $email): EmployeeEntity|null
    {
        $row = $this->dbconn->table('employee')->where('email', $email)->fetch();
        return EmployeeDbMapper::load($row);
    }
}
