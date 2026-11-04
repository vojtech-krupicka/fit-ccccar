<?php declare(strict_types=1);

namespace App\Core\Authentication;

use App\Model\Employee\EmployeeFacade;
use Nette\Security;

final class AdminAuthenticator implements Security\Authenticator
{
    private const DUMMY_HASH = '$2y$12$0FNPPaIgC4xKxMpG0CIr3uBBIqD3IROwAhErnBTiOHCbr4WEjSyje';

    public function __construct(
        private Security\Passwords $passwords,
        private EmployeeFacade $employeeFacade,
    ) {}

    public function authenticate(string $username, string $password): Identity
    {
        $admin = $this->employeeFacade->getByEmail($username);
        if (!$admin) {
            $this->passwords->verify($password, self::DUMMY_HASH);
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
