<?php declare(strict_types=1);

namespace App\Core\Authentication;

use App\Model\Employee\EmployeeFacade;
use Nette\Security;

final class AdminAuthenticator implements Security\Authenticator
{
    public function __construct(
        private Security\Passwords $passwords,
        private EmployeeFacade $employeeFacade,
    ) {}

    public function authenticate(string $username, string $password): Identity
    {
        $admin = $this->employeeFacade->getEmployeeByEmail($username);
        if (!$admin) {
            throw new Security\AuthenticationException('User not found.');
        }

        if (!$this->passwords->verify($password, $admin->password)) {
            throw new Security\AuthenticationException('Invalid password.');
        }

        return new Identity(
            id: (int) $admin->id,
            roles: ['admin'],
            email: (string) $admin->email,
            firstname: (string) $admin->firstname,
            surname: (string) $admin->surname,
        );
    }
}
