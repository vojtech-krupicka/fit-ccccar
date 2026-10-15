<?php declare(strict_types=1);

namespace App\Model\Employee;

use App\Model\BaseRepository;

class EmployeeRepository extends BaseRepository
{
    public function __construct(
        private readonly \Nette\Database\Explorer $dbconn,
        private readonly EmployeeDbMapper $employeeDbMapper,
    ) {}

    public function get(int $id): EmployeeEntity|null
    {
        $row = $this->dbconn->table('employee')->get($id);
        return $this->employeeDbMapper->load($row);
    }

    public function getByEmail(string $email): EmployeeEntity|null
    {
        $row = $this->dbconn->table('employee')->where('email', $email)->fetch();
        return $this->employeeDbMapper->load($row);
    }

    public function insert(EmployeeEntity $employee): void
    {
        $row = $this->dbconn->table('employee')->insert($this->employeeDbMapper->dump($employee));
        $employee->id = (int) $row['id'];
    }

    public function update(EmployeeEntity $employee): void
    {
        $row = $this->dbconn->table('employee')->where('id', $employee->id);
        if ($row != null) {
            $row->update($this->employeeDbMapper->dump($employee));
        }
    }
}
