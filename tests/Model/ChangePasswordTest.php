<?php declare(strict_types=1);

namespace Tests\Model;

use App\Model\Customer\CustomerFacade;
use App\Model\Employee\EmployeeFacade;
use Nette\Security\Passwords;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\CustomerFixture;
use Tests\DatabaseTestCase;

#[CoversClass(CustomerFacade::class)]
#[CoversClass(EmployeeFacade::class)]
final class ChangePasswordTest extends DatabaseTestCase
{
    use CustomerFixture;

    private const OLD = 'Stare-Heslo-1';
    private const NEW = 'Nove-Heslo-22';

    public function testCustomerPasswordIsChangedWhenTheOldOneMatches(): void
    {
        $facade = self::container()->getByType(CustomerFacade::class);
        $customer = $this->createCustomer('change@example.com', self::OLD);

        $this->assertTrue($facade->changePassword($customer, self::OLD, self::NEW));

        $stored = $facade->get((int) $customer->id);
        $passwords = self::container()->getByType(Passwords::class);
        $this->assertNotNull($stored);
        $this->assertTrue($passwords->verify(self::NEW, $stored->password), 'the new password works');
        $this->assertFalse($passwords->verify(self::OLD, $stored->password), 'the old password no longer works');
        $this->assertNotSame(self::NEW, $stored->password, 'stored hashed, not in plain text');
    }

    public function testCustomerPasswordIsKeptWhenTheOldOneIsWrong(): void
    {
        $facade = self::container()->getByType(CustomerFacade::class);
        $customer = $this->createCustomer('keep@example.com', self::OLD);
        $hashBefore = $customer->password;

        $this->assertFalse($facade->changePassword($customer, 'not-the-old-password', self::NEW));

        $this->assertSame($hashBefore, $facade->get((int) $customer->id)?->password);
    }

    public function testEmployeePasswordIsChangedWhenTheOldOneMatches(): void
    {
        $facade = self::container()->getByType(EmployeeFacade::class);
        $passwords = self::container()->getByType(Passwords::class);
        $employee = $facade->get(1);
        $this->assertNotNull($employee);
        $employee->password = $passwords->hash(self::OLD);
        $facade->persist($employee);

        $this->assertTrue($facade->changePassword($employee, self::OLD, self::NEW));

        $stored = $facade->get(1);
        $this->assertNotNull($stored);
        $this->assertTrue($passwords->verify(self::NEW, $stored->password));
    }

    public function testEmployeePasswordIsKeptWhenTheOldOneIsWrong(): void
    {
        $facade = self::container()->getByType(EmployeeFacade::class);
        $employee = $facade->get(1);
        $this->assertNotNull($employee);
        $hashBefore = $employee->password;

        $this->assertFalse($facade->changePassword($employee, 'not-the-old-password', self::NEW));

        $this->assertSame($hashBefore, $facade->get(1)?->password);
    }
}
