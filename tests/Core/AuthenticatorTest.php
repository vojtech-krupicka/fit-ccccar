<?php declare(strict_types=1);

namespace Tests\Core;

use App\Core\Authentication\AdminAuthenticator;
use App\Core\Authentication\FrontAuthenticator;
use App\Model\Customer\CustomerEntity;
use App\Model\Customer\CustomerFacade;
use App\Model\Employee\EmployeeFacade;
use Nette\Security\AuthenticationException;
use Nette\Security\Passwords;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\DatabaseTestCase;

#[CoversClass(FrontAuthenticator::class)]
#[CoversClass(AdminAuthenticator::class)]
final class AuthenticatorTest extends DatabaseTestCase
{
    private const PASSWORD = 'Tajne-Heslo-1';

    public function testCustomerCanSignInWithTheRightPassword(): void
    {
        $customer = $this->createCustomer('login@example.com');

        $identity = self::container()->getByType(FrontAuthenticator::class)
            ->authenticate('login@example.com', self::PASSWORD);

        $this->assertSame($customer->id, $identity->getId());
        $this->assertSame(['customer'], $identity->getRoles());
    }

    public function testCustomerWithWrongPasswordIsRejected(): void
    {
        $this->createCustomer('wrong@example.com');

        $this->expectException(AuthenticationException::class);
        self::container()->getByType(FrontAuthenticator::class)->authenticate('wrong@example.com', 'not-the-password');
    }

    public function testUnknownCustomerIsRejected(): void
    {
        $this->expectException(AuthenticationException::class);
        self::container()->getByType(FrontAuthenticator::class)->authenticate('nobody@example.com', self::PASSWORD);
    }

    public function testEmployeeCanSignInAsAdmin(): void
    {
        $facade = self::container()->getByType(EmployeeFacade::class);
        $employee = $facade->get(1);
        $this->assertNotNull($employee);
        $employee->password = self::container()->getByType(Passwords::class)->hash(self::PASSWORD);
        $facade->persist($employee);

        $identity = self::container()->getByType(AdminAuthenticator::class)
            ->authenticate($employee->email, self::PASSWORD);

        $this->assertSame(1, $identity->getId());
        $this->assertSame(['admin'], $identity->getRoles());
    }

    public function testACustomerCannotSignInToTheAdmin(): void
    {
        $this->createCustomer('customer@example.com');

        $this->expectException(AuthenticationException::class);
        self::container()->getByType(AdminAuthenticator::class)->authenticate('customer@example.com', self::PASSWORD);
    }

    private function createCustomer(string $email): CustomerEntity
    {
        $customer = new CustomerEntity(
            id: null,
            password: self::container()->getByType(Passwords::class)->hash(self::PASSWORD),
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
