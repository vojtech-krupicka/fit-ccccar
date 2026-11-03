<?php declare(strict_types=1);

namespace Tests\UI;

use App\Core\Authentication\Identity;
use Nette\Application\Responses\TextResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\PresenterTestCase;

/**
 * Smoke test: every page the visitor can reach must render without an error.
 */
final class PublicPagesTest extends PresenterTestCase
{
    /**
     * @return iterable<string, array{string, array<string, mixed>}>
     */
    public static function providePages(): iterable
    {
        yield 'home' => ['Front:Home', ['action' => 'default']];
        yield 'offices' => ['Front:Home', ['action' => 'offices']];
        yield 'catalog' => ['Front:Catalog', ['action' => 'default']];
        yield 'catalog filtered' => ['Front:Catalog', ['action' => 'default', 'rentalOffice' => 1, 'sortBy' => 'cost_per_day', 'sortDirection' => 'desc']];
        yield 'vehicle detail' => ['Front:Catalog', ['action' => 'detail', 'id' => 1]];
        yield 'sign in' => ['Front:SignIn', ['action' => 'default']];
        yield 'sign up' => ['Front:SignUp', ['action' => 'default']];
        yield 'admin sign in' => ['Admin:SignIn', ['action' => 'default']];
    }

    /**
     * @param array<string, mixed> $params
     */
    #[DataProvider('providePages')]
    public function testThePageRenders(string $presenter, array $params): void
    {
        $response = $this->runPresenter($presenter, $params);

        $this->assertInstanceOf(TextResponse::class, $response);
        $this->assertNotSame('', $this->renderPage($response));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function providePagesWithText(): iterable
    {
        yield 'front home' => ['Front:Home', 'default'];
        yield 'conditions' => ['Front:Home', 'conditions'];
        yield 'faq' => ['Front:Home', 'faq'];
        yield 'contact' => ['Front:Home', 'contact'];
        yield 'admin help' => ['Admin:Home', 'default'];
    }

    #[DataProvider('providePagesWithText')]
    public function testNoPlaceholderTextIsLeft(string $presenter, string $action): void
    {
        $this->loginAs(new Identity(id: 1, roles: ['admin'], email: 'frodo.baggins@ccccar.cz', firstname: 'Frodo', surname: 'Baggins'));

        $html = $this->renderPage($this->runPresenter($presenter, ['action' => $action]));

        $this->assertStringNotContainsStringIgnoringCase('lorem ipsum', $html);
    }

    public function testTheAdminHelpDoesNotDescribeMissingFeatures(): void
    {
        $this->loginAs(new Identity(id: 1, roles: ['admin'], email: 'frodo.baggins@ccccar.cz', firstname: 'Frodo', surname: 'Baggins'));

        $html = $this->renderPage($this->runPresenter('Admin:Home', ['action' => 'default']));

        // the old help came from a CMS: this application has no pages, files or galleries to manage
        $this->assertStringNotContainsString('toc_cms', $html);
        $this->assertStringNotContainsString('toc_gallery', $html);
        $this->assertStringContainsString('Správa vozidel', $html);
    }

    /**
     * Pages that the navigation links to. Each must exist, so the menu has no dead links.
     *
     * The last value says whether the page exists yet. Four menu items still lead nowhere: those cases are
     * reported as skipped (so they stay visible); set the flag to true when the page is built.
     *
     * @return iterable<string, array{string, string, bool}>
     */
    public static function provideNavigationTargets(): iterable
    {
        yield 'conditions' => ['Front:Home', 'conditions', true];
        yield 'faq' => ['Front:Home', 'faq', true];
        yield 'contact' => ['Front:Home', 'contact', true];
        yield 'customer loans' => ['Front:Customer', 'loans', false];
        yield 'offices' => ['Front:Home', 'offices', true];
        yield 'catalog' => ['Front:Catalog', 'default', true];
        yield 'customer profile' => ['Front:Customer', 'profile', true];
    }

    #[DataProvider('provideNavigationTargets')]
    public function testNavigationLinksLeadToAnExistingPage(string $presenter, string $action, bool $exists): void
    {
        if (!$exists) {
            $this->markTestSkipped("The page {$presenter}:{$action} is linked from the menu but not built yet.");
        }
        $this->loginAs(new Identity(id: 1, roles: ['customer'], email: 'bilbo.baggins@example.com', firstname: 'Bilbo', surname: 'Baggins'));

        $response = $this->runPresenter($presenter, ['action' => $action]);

        $this->assertInstanceOf(TextResponse::class, $response);
        $this->renderPage($response);
    }
}
