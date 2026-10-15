<?php declare(strict_types=1);

namespace App\Model\Employee;

use Nette\Security\Passwords;

class EmployeeFacade
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
        private Passwords $passwords,
    ) {}

    public function getEmployee(int $id): EmployeeEntity|null
    {
        return $this->employeeRepository->get($id);
    }

    public function getEmployeeByEmail(string $email): EmployeeEntity|null
    {
        return $this->employeeRepository->getByEmail($email);
    }

    public function persist(EmployeeEntity $employee): void
    {
        if ($employee->id === null) {
            $this->employeeRepository->insert($employee);
        } else {
            $this->employeeRepository->update($employee);
        }
    }

    public function changePassword(EmployeeEntity $employee, string $oldPassword, string $newPassword): bool
    {
        if (!$this->passwords->verify($oldPassword, $employee->password)) {
            return false;
        }

        $employee->password = $this->passwords->hash($newPassword);
        $this->persist($employee);
        return true;
    }
}
