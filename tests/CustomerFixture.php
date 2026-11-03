<?php declare(strict_types=1);

namespace Tests;

use App\Model\Customer\CustomerEntity;
use App\Model\Customer\CustomerFacade;
use Nette\Security\Passwords;

/**
 * Creates customers that can really sign in (their password is hashed like in the application).
 *
 * @phpstan-require-extends DatabaseTestCase
 */
trait CustomerFixture
{
    protected function createCustomer(string $email, string $password = 'Tajne-Heslo-1'): CustomerEntity
    {
        $customer = new CustomerEntity(
            id: null,
            password: self::container()->getByType(Passwords::class)->hash($password),
            email: $email,
            firstname: 'Test',
            surname: 'User',
            identityNumber: '123456789',
            phone: '123456789',
            street: 'Street 1',
            city: 'Praha',
            zip: '11000',
            country: 'cz',
        );
        $this->assertTrue(self::container()->getByType(CustomerFacade::class)->persist($customer));

        return $customer;
    }
}
