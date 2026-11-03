<?php declare(strict_types=1);

namespace Tests\UI;

use App\Model\Customer\CustomerFacade;
use Nette\Application\Responses\RedirectResponse;
use Nette\Security\Passwords;
use Tests\CustomerFixture;
use Tests\PresenterTestCase;

final class SignUpTest extends PresenterTestCase
{
    use CustomerFixture;

    /**
     * @param array<string, string> $overrides
     * @return array<string, string>
     */
    private function validPost(array $overrides = []): array
    {
        return $overrides + [
            'email' => 'new.customer@example.com',
            'password' => 'Silne-Heslo-123',
            'passwordConfirm' => 'Silne-Heslo-123',
            'firstName' => 'Nový',
            'lastName' => 'Zákazník',
            'idNumber' => '123456789',
            'phone' => '+420123456789',
            'street' => 'Hlavní 1',
            'city' => 'Praha',
            'zip' => '11000',
            'country' => 'cz',
            'aggred' => '1',
            'send' => 'Registrovat',
        ];
    }

    private function signUp(array $post): \Nette\Application\Response
    {
        return $this->submitForm('Front:SignUp', 'signUpForm', $post, ['action' => 'default']);
    }

    public function testRegistrationCreatesACustomerWithAHashedPassword(): void
    {
        $response = $this->signUp($this->validPost());

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $customer = self::container()->getByType(CustomerFacade::class)->getByEmail('new.customer@example.com');
        $this->assertNotNull($customer);
        $this->assertNotSame('Silne-Heslo-123', $customer->password, 'the password must never be stored in plain text');
        $this->assertTrue(self::container()->getByType(Passwords::class)->verify('Silne-Heslo-123', $customer->password));
    }

    public function testRegisteringAnExistingEmailFails(): void
    {
        $this->createCustomer('taken@example.com');

        $response = $this->signUp($this->validPost(['email' => 'taken@example.com']));

        $this->assertNotInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('Registrace se nezdařila', $this->renderPage($response));
        $count = self::container()->getByType(\Nette\Database\Explorer::class)
            ->table('customer')->where('email', 'taken@example.com')->count('*');
        $this->assertSame(1, $count);
    }

    public function testMismatchingPasswordsAreRejected(): void
    {
        $response = $this->signUp($this->validPost(['passwordConfirm' => 'Jine-Heslo-123']));

        $this->assertStringContainsString('Hesla se neshodují!', $this->renderPage($response));
        $this->assertNull(self::container()->getByType(CustomerFacade::class)->getByEmail('new.customer@example.com'));
    }

    public function testWeakPasswordIsRejected(): void
    {
        $response = $this->signUp($this->validPost(['password' => 'abc', 'passwordConfirm' => 'abc']));

        $this->assertNotInstanceOf(RedirectResponse::class, $response);
        $this->assertNull(self::container()->getByType(CustomerFacade::class)->getByEmail('new.customer@example.com'));
    }

    public function testTermsMustBeAccepted(): void
    {
        $post = $this->validPost();
        unset($post['aggred']);

        $response = $this->signUp($post);

        $this->assertStringContainsString('Musíte souhlasit s obchodními podmínkami.', $this->renderPage($response));
        $this->assertNull(self::container()->getByType(CustomerFacade::class)->getByEmail('new.customer@example.com'));
    }

    public function testUnknownCountryIsRejected(): void
    {
        $response = $this->signUp($this->validPost(['country' => 'xx']));

        $this->assertNotInstanceOf(RedirectResponse::class, $response);
        $this->assertNull(self::container()->getByType(CustomerFacade::class)->getByEmail('new.customer@example.com'));
    }
}
