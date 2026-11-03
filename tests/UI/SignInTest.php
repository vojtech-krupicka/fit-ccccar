<?php declare(strict_types=1);

namespace Tests\UI;

use App\Model\Employee\EmployeeFacade;
use Nette\Application\Responses\RedirectResponse;
use Nette\Security\Passwords;
use Nette\Security\User;
use Tests\CustomerFixture;
use Tests\PresenterTestCase;

final class SignInTest extends PresenterTestCase
{
    use CustomerFixture;

    private const PASSWORD = 'Tajne-Heslo-1';

    public function testCustomerSignsInWithTheRightPassword(): void
    {
        $this->createCustomer('login@example.com', self::PASSWORD);

        $response = $this->submitForm('Front:SignIn', 'signInForm', [
            'username' => 'login@example.com',
            'password' => self::PASSWORD,
            'send' => 'Přihlásit',
        ], ['action' => 'default']);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue(self::container()->getByType(User::class)->isLoggedIn());
    }

    public function testCustomerWithWrongPasswordGetsAGenericError(): void
    {
        $this->createCustomer('login@example.com', self::PASSWORD);

        $response = $this->submitForm('Front:SignIn', 'signInForm', [
            'username' => 'login@example.com',
            'password' => 'wrong-password',
            'send' => 'Přihlásit',
        ], ['action' => 'default']);

        $this->assertFalse(self::container()->getByType(User::class)->isLoggedIn());
        $html = $this->renderPage($response);
        $this->assertStringContainsString('Nesprávné přihlašovací jméno nebo heslo!', $html);
    }

    public function testUnknownCustomerGetsTheSameErrorAsAWrongPassword(): void
    {
        $response = $this->submitForm('Front:SignIn', 'signInForm', [
            'username' => 'nobody@example.com',
            'password' => 'whatever',
            'send' => 'Přihlásit',
        ], ['action' => 'default']);

        $this->assertFalse(self::container()->getByType(User::class)->isLoggedIn());
        $this->assertStringContainsString('Nesprávné přihlašovací jméno nebo heslo!', $this->renderPage($response));
    }

    public function testEmptyFormIsRejected(): void
    {
        $response = $this->submitForm('Front:SignIn', 'signInForm', ['send' => 'Přihlásit'], ['action' => 'default']);

        $this->assertFalse(self::container()->getByType(User::class)->isLoggedIn());
        $this->assertStringContainsString('Vyplňte prosím Vaše heslo.', $this->renderPage($response));
    }

    public function testACustomerCannotSignInToTheAdmin(): void
    {
        $this->createCustomer('customer@example.com', self::PASSWORD);

        $response = $this->submitForm('Admin:SignIn', 'signInForm', [
            'username' => 'customer@example.com',
            'password' => self::PASSWORD,
            'send' => 'Přihlásit',
        ], ['action' => 'default']);

        $this->assertFalse(self::container()->getByType(User::class)->isLoggedIn());
        $this->assertStringContainsString('Uživatelské jméno nebo heslo nesouhlasí.', $this->renderPage($response));
    }

    public function testEmployeeSignsInToTheAdmin(): void
    {
        $facade = self::container()->getByType(EmployeeFacade::class);
        $employee = $facade->get(1);
        $this->assertNotNull($employee);
        $employee->password = self::container()->getByType(Passwords::class)->hash(self::PASSWORD);
        $facade->persist($employee);

        $response = $this->submitForm('Admin:SignIn', 'signInForm', [
            'username' => $employee->email,
            'password' => self::PASSWORD,
            'send' => 'Přihlásit',
        ], ['action' => 'default']);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue(self::container()->getByType(User::class)->isLoggedIn());
    }
}
