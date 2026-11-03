<?php declare(strict_types=1);

namespace Tests\UI;

use App\Core\Authentication\Identity;
use App\Model\Customer\CustomerEntity;
use App\Model\Customer\CustomerFacade;
use Nette\Application\Responses\RedirectResponse;
use Nette\Application\Responses\TextResponse;
use Nette\Security\Passwords;
use Tests\CustomerFixture;
use Tests\PresenterTestCase;

final class CustomerProfileTest extends PresenterTestCase
{
    use CustomerFixture;

    private const PASSWORD = 'Tajne-Heslo-1';

    private CustomerEntity $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = $this->createCustomer('profile@example.com', self::PASSWORD);
        $this->loginAs(new Identity(
            id: (int) $this->customer->id,
            roles: ['customer'],
            email: 'profile@example.com',
            firstname: 'Test',
            surname: 'User',
        ));
    }

    private function stored(): CustomerEntity
    {
        $customer = self::container()->getByType(CustomerFacade::class)->get((int) $this->customer->id);
        $this->assertNotNull($customer);

        return $customer;
    }

    /**
     * @param array<string, string> $overrides
     * @return array<string, string>
     */
    private function profilePost(array $overrides = []): array
    {
        return $overrides + [
            'email' => 'profile@example.com',
            'firstName' => 'Test',
            'lastName' => 'User',
            'idNumber' => '123456789',
            'phone' => '123456789',
            'street' => 'Street 1',
            'city' => 'Praha',
            'zip' => '11000',
            'country' => 'cz',
            'send' => 'Uložit',
        ];
    }

    /**
     * @param array<string, string> $post
     */
    private function changePassword(array $post): \Nette\Application\Response
    {
        return $this->submitForm('Front:Customer', 'changePasswordForm', $post + ['send' => 'Změnit heslo'], ['action' => 'profile']);
    }

    public function testGuestsAreSentToTheSignInPage(): void
    {
        $this->loginAs(new Identity(id: 1, roles: ['customer'], email: 'x', firstname: 'x', surname: 'x'));
        $this->keepIdentityButLogOut(new Identity(id: 1, roles: ['customer'], email: 'x', firstname: 'x', surname: 'x'));

        $response = $this->runPresenter('Front:Customer', ['action' => 'profile']);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('prihlaseni', $response->getUrl());
    }

    public function testTheProfilePageShowsTheCustomersOwnData(): void
    {
        $html = $this->renderPage($this->runPresenter('Front:Customer', ['action' => 'profile']));

        // Nette writes "@" in attribute values as &#64; (against address harvesters)
        $this->assertStringContainsString('profile&#64;example.com', $html);
    }

    public function testProfileIsUpdated(): void
    {
        $response = $this->submitForm('Front:Customer', 'profileForm', $this->profilePost(['city' => 'Brno', 'phone' => '+420987654321']), ['action' => 'profile']);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('Brno', $this->stored()->city);
        $this->assertSame('+420987654321', $this->stored()->phone);
    }

    public function testAnInvalidPhoneIsRejected(): void
    {
        $response = $this->submitForm('Front:Customer', 'profileForm', $this->profilePost(['phone' => 'not-a-phone']), ['action' => 'profile']);

        $this->assertNotInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('123456789', $this->stored()->phone);
    }

    public function testAnEmailUsedByAnotherCustomerIsRefused(): void
    {
        $response = $this->submitForm('Front:Customer', 'profileForm', $this->profilePost(['email' => 'rosie.cotton@example.com']), ['action' => 'profile']);

        $this->assertSame('profile@example.com', $this->stored()->email, 'the e-mail must not change');
        $this->assertInstanceOf(TextResponse::class, $response, 'the form is shown again with an error, not reported as saved');
        $this->assertStringNotContainsString('úspěšně aktualizován', $this->renderPage($response));
    }

    public function testPasswordIsChanged(): void
    {
        $response = $this->changePassword([
            'oldPassword' => self::PASSWORD,
            'newPassword' => 'Nove-Heslo-22',
            'newPasswordConfirm' => 'Nove-Heslo-22',
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue(self::container()->getByType(Passwords::class)->verify('Nove-Heslo-22', $this->stored()->password));
        $html = $this->followRedirect($response, 'Front:Customer', ['action' => 'profile']);
        $this->assertStringContainsString('Vaše heslo bylo úspěšně aktualizováno.', $html);
    }

    public function testPasswordIsKeptWhenTheOldOneIsWrong(): void
    {
        $hash = $this->stored()->password;

        $response = $this->changePassword([
            'oldPassword' => 'wrong-old-password',
            'newPassword' => 'Nove-Heslo-22',
            'newPasswordConfirm' => 'Nove-Heslo-22',
        ]);

        $this->assertSame($hash, $this->stored()->password);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('Zkontrolujte prosím staré heslo.', $this->followRedirect($response, 'Front:Customer', ['action' => 'profile']));
    }

    public function testMismatchingNewPasswordsAreRejected(): void
    {
        $hash = $this->stored()->password;

        $response = $this->changePassword([
            'oldPassword' => self::PASSWORD,
            'newPassword' => 'Nove-Heslo-22',
            'newPasswordConfirm' => 'Jine-Heslo-333',
        ]);

        $this->assertNotInstanceOf(RedirectResponse::class, $response);
        $this->assertSame($hash, $this->stored()->password);
    }

    public function testAWeakNewPasswordIsRejected(): void
    {
        $hash = $this->stored()->password;

        $this->changePassword(['oldPassword' => self::PASSWORD, 'newPassword' => 'abc', 'newPasswordConfirm' => 'abc']);

        $this->assertSame($hash, $this->stored()->password);
    }
}
