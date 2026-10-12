<?php declare(strict_types=1);

namespace App\Core\Authentication;

use App\Model\Customer\CustomerFacade;
use Nette\Security;

final class FrontAuthenticator implements Security\Authenticator
{
    public function __construct(
        private Security\Passwords $passwords,
        private CustomerFacade $customerFacade,
    ) {}

    public function authenticate(string $username, string $password): Identity
    {
        $user = $this->customerFacade->getCustomerByEmail($username);
        if (!$user) {
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
