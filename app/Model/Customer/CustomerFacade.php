<?php declare(strict_types=1);

namespace App\Model\Customer;

use App\Model\BaseFacade;
use Nette\Security\Passwords;

/**
 * @extends BaseFacade<CustomerEntity, CustomerRepository>
 */
class CustomerFacade extends BaseFacade
{
    public function __construct(
        CustomerRepository $repository,
        private Passwords $passwords,
    ) {
        parent::__construct($repository);
    }

    public function getByEmail(string $email): CustomerEntity|null
    {
        return $this->repository->getByEmail($email);
    }

    public function changePassword(CustomerEntity $customer, string $oldPassword, string $newPassword): bool
    {
        if (!$this->passwords->verify($oldPassword, $customer->password)) {
            return false;
        }

        $customer->password = $this->passwords->hash($newPassword);
        $this->persist($customer);
        return true;
    }
}
