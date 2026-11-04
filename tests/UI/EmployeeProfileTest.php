<?php declare(strict_types=1);

namespace Tests\UI;

use App\Core\Authentication\Identity;
use App\Model\Employee\EmployeeEntity;
use App\Model\Employee\EmployeeFacade;
use Nette\Application\Responses\RedirectResponse;
use Nette\Security\Passwords;
use Tests\PresenterTestCase;

final class EmployeeProfileTest extends PresenterTestCase
{
    private const PASSWORD = 'Tajne-Heslo-1';

    protected function setUp(): void
    {
        parent::setUp();
        $facade = self::container()->getByType(EmployeeFacade::class);
        $employee = $facade->get(1);
        $this->assertNotNull($employee);
        $employee->password = self::container()->getByType(Passwords::class)->hash(self::PASSWORD);
        $facade->persist($employee);
        $this->loginAs(new Identity(id: 1, roles: ['admin'], email: $employee->email, firstname: $employee->firstname, surname: $employee->surname));
    }

    private function stored(): EmployeeEntity
    {
        $employee = self::container()->getByType(EmployeeFacade::class)->get(1);
        $this->assertNotNull($employee);

        return $employee;
    }

    /**
     * @param array<string, string> $overrides
     * @return array<string, string>
     */
    private function profilePost(array $overrides = []): array
    {
        return $overrides + [
            'rentalOffice' => '2',
            'email' => 'frodo.new@ccccar.cz',
            'firstName' => 'Frodo',
            'lastName' => 'Baggins',
            'phone' => '123456789',
            'send' => 'Uložit',
        ];
    }

    /**
     * @param array<string, string> $post
     */
    private function profile(array $post): \Nette\Application\Response
    {
        return $this->submitForm('Admin:Employee', 'profileForm', $post, ['action' => 'profile']);
    }

    public function testProfileIsUpdated(): void
    {
        $response = $this->profile($this->profilePost());

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('frodo.new@ccccar.cz', $this->stored()->email);
        $this->assertSame(2, $this->stored()->rentalOfficeId);
    }

    public function testTheRentalOfficeIsRequired(): void
    {
        $post = $this->profilePost();
        unset($post['rentalOffice']);
        $emailBefore = $this->stored()->email;

        $response = $this->profile($post);

        $this->assertNotInstanceOf(RedirectResponse::class, $response, 'a missing office is a validation error, not a crash');
        $this->assertSame($emailBefore, $this->stored()->email);
    }

    public function testAnUnknownRentalOfficeIsRejected(): void
    {
        $response = $this->profile($this->profilePost(['rentalOffice' => '999999']));

        $this->assertNotInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(1, $this->stored()->rentalOfficeId);
    }

    public function testAnEmailUsedByAnotherEmployeeIsRefused(): void
    {
        $emailBefore = $this->stored()->email;

        $response = $this->profile($this->profilePost(['email' => 'samwise.gamgee@ccccar.cz']));

        $this->assertSame($emailBefore, $this->stored()->email);
        $this->assertNotInstanceOf(RedirectResponse::class, $response, 'the form is shown again with an error, not reported as saved');
    }

    public function testPasswordIsChanged(): void
    {
        $response = $this->submitForm('Admin:Employee', 'changePasswordForm', [
            'oldPassword' => self::PASSWORD,
            'newPassword' => 'Nove-Heslo-22',
            'newPasswordConfirm' => 'Nove-Heslo-22',
            'send' => 'Změnit heslo',
        ], ['action' => 'profile']);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue(self::container()->getByType(Passwords::class)->verify('Nove-Heslo-22', $this->stored()->password));
    }

    public function testPasswordIsKeptWhenTheOldOneIsWrong(): void
    {
        $hash = $this->stored()->password;

        $response = $this->submitForm('Admin:Employee', 'changePasswordForm', [
            'oldPassword' => 'wrong-old-password',
            'newPassword' => 'Nove-Heslo-22',
            'newPasswordConfirm' => 'Nove-Heslo-22',
            'send' => 'Změnit heslo',
        ], ['action' => 'profile']);

        $this->assertSame($hash, $this->stored()->password);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('Zkontrolujte prosím staré heslo.', $this->followRedirect($response, 'Admin:Employee', ['action' => 'profile']));
    }
}
