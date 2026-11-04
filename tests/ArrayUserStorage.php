<?php declare(strict_types=1);

namespace Tests;

use Nette\Security\IIdentity;
use Nette\Security\User;
use Nette\Security\UserStorage;

/**
 * In-memory user storage for tests: no session is started, so login state cannot leak between tests.
 */
final class ArrayUserStorage implements UserStorage
{
    private bool $authenticated = false;
    private ?IIdentity $identity = null;
    private ?int $reason = null;

    public function saveAuthentication(IIdentity $identity): void
    {
        $this->authenticated = true;
        $this->identity = $identity;
        $this->reason = null;
    }

    public function clearAuthentication(bool $clearIdentity): void
    {
        $this->authenticated = false;
        $this->reason = User::LogoutManual;
        if ($clearIdentity) {
            $this->identity = null;
        }
    }

    /** @return array{bool, ?IIdentity, ?int} */
    public function getState(): array
    {
        return [$this->authenticated, $this->identity, $this->reason];
    }

    public function setExpiration(?string $expire, bool $clearIdentity): void {}
}
