<?php declare(strict_types=1);

namespace App\Core\Authentization;

use App\Model\BaseEntity;
use Nette\Security;

class Identity implements Security\IIdentity
{
    public readonly string $fullname;

    /**
     * @param list<string> $roles
     */
    public function __construct(
        public readonly int $id,
        public readonly array $roles,
        public readonly string $email,
        public readonly string $firstname,
        public readonly string $surname
    ) {
        $this->fullname = $firstname . ' ' . $surname;
    }

    /**
     * Returns the ID of user.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns a list of roles that the user is a member of.
     * @return list<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
}
