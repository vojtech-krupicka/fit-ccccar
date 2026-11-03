<?php declare(strict_types=1);

namespace Tests\UI;

use App\Core\Authentication\Identity;
use Nette\Application\BadRequestException;
use Nette\Application\Responses\RedirectResponse;
use Nette\Application\Responses\TextResponse;
use Nette\Security\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\PresenterTestCase;

final class AdminAccessTest extends PresenterTestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideAdminPages(): iterable
    {
        yield 'home' => ['Admin:Home', 'default'];
        yield 'vehicles' => ['Admin:Vehicle', 'default'];
        yield 'customers' => ['Admin:Customer', 'default'];
        yield 'rental offices' => ['Admin:RentalOffice', 'default'];
        yield 'car rentals' => ['Admin:CarRental', 'default'];
        yield 'employee profile' => ['Admin:Employee', 'profile'];
    }

    #[DataProvider('provideAdminPages')]
    public function testGuestsAreSentToTheSignInPage(string $presenter, string $action): void
    {
        $response = $this->runPresenter($presenter, ['action' => $action]);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('prihlaseni', $response->getUrl());
    }

    #[DataProvider('provideAdminPages')]
    public function testAdminsSeeThePages(string $presenter, string $action): void
    {
        $this->loginAsAdmin();

        $response = $this->runPresenter($presenter, ['action' => $action]);

        $this->assertInstanceOf(TextResponse::class, $response);
        $this->renderPage($response);
    }

    /**
     * @return iterable<string, array{string, array<string, mixed>}>
     */
    public static function provideDestructiveActions(): iterable
    {
        yield 'delete vehicle' => ['Admin:Vehicle', ['action' => 'delete', 'id' => 1]];
        yield 'delete customer' => ['Admin:Customer', ['action' => 'delete', 'id' => 1]];
        yield 'delete car rental' => ['Admin:CarRental', ['action' => 'delete', 'id' => 1]];
        yield 'delete rental office' => ['Admin:RentalOffice', ['action' => 'delete', 'id' => 1]];
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('provideDestructiveActions')]
    public function testDeleteRefusesGetRequests(string $presenter, array $params): void
    {
        $this->loginAsAdmin();

        $this->expectException(BadRequestException::class);
        $this->runPresenter($presenter, $params);
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('provideDestructiveActions')]
    public function testDeleteAcceptsASameOriginPost(string $presenter, array $params): void
    {
        $this->loginAsAdmin();

        $response = $this->runPresenter($presenter, $params, 'POST');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('_fid=', $response->getUrl(), 'the action ran and left a flash message');
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('provideDestructiveActions')]
    public function testDeleteRefusesACrossSitePost(string $presenter, array $params): void
    {
        $this->loginAsAdmin();
        $this->fetchSite = 'cross-site';

        $response = $this->runPresenter($presenter, $params, 'POST');

        // Nette answers a cross-site request by redirecting back to the same URL; the action itself must not run
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringNotContainsString('_fid=', $response->getUrl(), 'the action must not run');
        $this->assertStringContainsString('smazat', $response->getUrl());
    }

    /**
     * @return iterable<string, array{string, array<string, mixed>}>
     */
    public static function providePlaceholderActions(): iterable
    {
        foreach (['Admin:Vehicle', 'Admin:Customer', 'Admin:RentalOffice', 'Admin:CarRental'] as $presenter) {
            yield "$presenter add" => [$presenter, ['action' => 'add']];
            yield "$presenter edit" => [$presenter, ['action' => 'edit', 'id' => 1]];
        }
    }

    /**
     * The grids link to add/edit on every list, so each page must answer (with a notice that it is not built yet)
     * instead of a "page not found".
     *
     * @param array<string, mixed> $params
     */
    #[DataProvider('providePlaceholderActions')]
    public function testEveryGridLinkLeadsSomewhere(string $presenter, array $params): void
    {
        $this->loginAsAdmin();

        $response = $this->runPresenter($presenter, $params);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('_fid=', $response->getUrl(), 'a notice explains that the function is missing');
    }

    public function testSignOutRefusesGetRequests(): void
    {
        $this->loginAsAdmin();

        $this->expectException(BadRequestException::class);
        $this->runPresenter('Admin:SignOut', ['action' => 'default']);
    }

    public function testSignOutLogsTheUserOut(): void
    {
        $this->loginAsAdmin();

        $response = $this->runPresenter('Admin:SignOut', ['action' => 'default'], 'POST');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertFalse(self::container()->getByType(User::class)->isLoggedIn());
    }

    public function testCustomerSignOutRefusesGetRequests(): void
    {
        $this->loginAs(new Identity(id: 1, roles: ['customer'], email: 'bilbo.baggins@example.com', firstname: 'Bilbo', surname: 'Baggins'));

        $this->expectException(BadRequestException::class);
        $this->runPresenter('Front:SignOut', ['action' => 'default']);
    }

    public function testCustomerSignOutLogsTheCustomerOut(): void
    {
        $this->loginAs(new Identity(id: 1, roles: ['customer'], email: 'bilbo.baggins@example.com', firstname: 'Bilbo', surname: 'Baggins'));

        $response = $this->runPresenter('Front:SignOut', ['action' => 'default'], 'POST');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertFalse(self::container()->getByType(User::class)->isLoggedIn());
    }

    private function loginAsAdmin(): void
    {
        $this->loginAs(new Identity(id: 1, roles: ['admin'], email: 'frodo.baggins@ccccar.cz', firstname: 'Frodo', surname: 'Baggins'));
    }
}
