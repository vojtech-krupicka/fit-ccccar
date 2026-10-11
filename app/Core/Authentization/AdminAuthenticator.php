<?php declare(strict_types=1);

namespace App\Core\Authentization;

use Nette\Security;

final class AdminAuthenticator implements Security\Authenticator
{
    public function __construct(
        private Security\Passwords $passwords,
    ) {}

    public function authenticate(string $username, string $password): Security\IIdentity
    {
        return new Security\SimpleIdentity(
            2026,
            ['admin'],
            ['name' => 'Harry Employee'],
        );
    }
}
