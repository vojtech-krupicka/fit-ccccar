<?php declare(strict_types=1);

namespace App\Model\Employee;

class EmployeeFacade
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
    ) {}

    public function getEmployee(int $id): EmployeeEntity|null
    {
        return $this->employeeRepository->get($id);
    }

    public function getEmployeeByEmail(string $email): EmployeeEntity|null
    {
        return $this->employeeRepository->getByEmail($email);
    }
}
