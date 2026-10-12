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

    public function persist(CustomerEntity $customer): void
    {
        if ($customer->id === null) {
            $this->customerRepository->insert($customer);
        } else {
            $this->customerRepository->update($customer);
        }
    }
}
