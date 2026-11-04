<?php declare(strict_types=1);

namespace App\Core\Authentication;

use App\Model\Customer\CustomerFacade;
use Nette\Security;

final class FrontAuthenticator implements Security\Authenticator
{
    private const DUMMY_HASH = '$2y$12$0FNPPaIgC4xKxMpG0CIr3uBBIqD3IROwAhErnBTiOHCbr4WEjSyje';

    public function __construct(
        private Security\Passwords $passwords,
        private CustomerFacade $customerFacade,
    ) {}

    public function authenticate(string $username, string $password): Identity
    {
        $user = $this->customerFacade->getByEmail($username);
        if (!$user) {
            $this->passwords->verify($password, self::DUMMY_HASH);
            throw new Security\AuthenticationException('User not found.');
        }

        if (!$this->passwords->verify($password, $user->password)) {
            throw new Security\AuthenticationException('Invalid password.');
        }

        return new Identity(
            id: (int) $user->id,
            roles: ['customer'],
            email: (string) $user->email,
            firstname: (string) $user->firstname,
            surname: (string) $user->surname,
        );
    }
}
