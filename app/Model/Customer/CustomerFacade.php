<?php declare(strict_types=1);

namespace App\Model\Customer;

class CustomerFacade
{
    public function __construct(
        private CustomerRepository $customerRepository,
    ) {}

    public function getCustomer(int $id): CustomerEntity|null
    {
        return $this->customerRepository->get($id);
    }

    public function getCustomerByEmail(string $email): CustomerEntity|null
    {
        return $this->customerRepository->getByEmail($email);
    }
}
