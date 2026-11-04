<?php declare(strict_types=1);

namespace App\Model\Employee;

use App\Model\BaseFacade;
use Nette\Security\Passwords;

/**
 * @extends BaseFacade<EmployeeEntity, EmployeeRepository>
 */
class EmployeeFacade extends BaseFacade
{
    public function __construct(
        EmployeeRepository $repository,
        private Passwords $passwords,
    ) {
        parent::__construct($repository);
    }

    public function getByEmail(string $email): EmployeeEntity|null
    {
        return $this->repository->getByEmail($email);
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
