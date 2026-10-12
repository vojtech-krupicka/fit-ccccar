<?php declare(strict_types=1);

namespace App\Model\Customer;

use Nette\Security\Passwords;

class CustomerFacade
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private Passwords $passwords,
    ) {}

    public function getCustomer(int $id): CustomerEntity|null
    {
        return $this->customerRepository->get($id);
    }

    public function getCustomerByEmail(string $email): CustomerEntity|null
    {
        return $this->customerRepository->getByEmail($email);
    }

    public function persist(CustomerEntity $customer): void
    {
        if ($customer->id === null) {
            $this->customerRepository->insert($customer);
        } else {
            $this->customerRepository->update($customer);
        }
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
