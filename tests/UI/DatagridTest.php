<?php declare(strict_types=1);

namespace Tests\UI;

use App\Core\Authentication\Identity;
use App\Model\Customer\CustomerEntity;
use App\Model\Customer\CustomerFacade;
use Nette\Application\Responses\RedirectResponse;
use Tests\PresenterTestCase;

final class DatagridTest extends PresenterTestCase
{
    private const BULK_FORM = 'defaultDatagrid-bulkActionForm';

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs(new Identity(id: 1, roles: ['admin'], email: 'frodo.baggins@ccccar.cz', firstname: 'Frodo', surname: 'Baggins'));
    }

    public function testValuesFromTheDatabaseAreEscaped(): void
    {
        $customer = new CustomerEntity(
            id: null,
            password: 'x',
            email: 'xss@example.com',
            firstname: '<script>alert(1)</script>',
            surname: 'Hacker',
            identityNumber: '123456789',
            phone: '<u>1</u>',
            street: '<b>Street</b>',
            city: 'Praha',
            zip: '11000',
            country: 'cz',
        );
        $this->assertTrue(self::container()->getByType(CustomerFacade::class)->persist($customer));

        $html = $this->renderPage($this->runPresenter('Admin:Customer', ['action' => 'default', 'sortDirection' => 'desc']));

        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt; Hacker', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('&lt;b&gt;Street&lt;/b&gt;', $html);
        // the phone is a plain text column: a bare string that only the template escapes
        $this->assertStringContainsString('&lt;u&gt;1&lt;/u&gt;', $html);
        $this->assertStringNotContainsString('<u>1</u>', $html);
        $this->assertStringNotContainsString('<b>Street</b>', $html);
    }

    public function testItemsPerPageIsLimitedToTheOfferedValues(): void
    {
        $html = $this->renderPage($this->runPresenter('Admin:Vehicle', ['action' => 'default', 'itemsPerPage' => 1000]));

        $this->assertStringContainsString('1 - 10 / 25', $html, 'an unknown page size falls back to the default of 10');
    }

    public function testAnOfferedItemsPerPageIsUsed(): void
    {
        $html = $this->renderPage($this->runPresenter('Admin:Vehicle', ['action' => 'default', 'itemsPerPage' => 5]));

        $this->assertStringContainsString('1 - 5 / 25', $html);
    }

    public function testUnknownSortColumnFallsBackToTheDefault(): void
    {
        $html = $this->renderPage($this->runPresenter('Admin:Vehicle', ['action' => 'default', 'sortBy' => 'id; DROP TABLE vehicle']));

        $this->assertStringContainsString('1 - 10 / 25', $html);
    }

    public function testBulkActionNeedsASelection(): void
    {
        $response = $this->bulk(['bulkAction' => 'activate']);

        $this->assertNotInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('Vyberte alespoň jednu položku.', $this->renderPage($response));
    }

    public function testBulkActionNeedsAnAction(): void
    {
        $response = $this->bulk(['bulkActionIds' => ['1', '2']]);

        $this->assertNotInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('Vyberte hromadnou akci, kterou chcete provést.', $this->renderPage($response));
    }

    public function testUnknownBulkActionIsRefused(): void
    {
        $response = $this->bulk(['bulkActionIds' => ['1'], 'bulkAction' => 'format-disk']);

        $this->assertNotInstanceOf(RedirectResponse::class, $response);
    }

    public function testBulkActionReceivesTheSelectedIds(): void
    {
        $response = $this->bulk(['bulkActionIds' => ['1', '3'], 'bulkAction' => 'activate']);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $html = $this->followRedirect($response, 'Admin:Vehicle', ['action' => 'default']);
        $this->assertStringContainsString('(1, 3)', $html);
    }

    public function testBulkActionIgnoresIdsThatAreNotOnThePage(): void
    {
        // vehicle 999 is not on the displayed page, so it must not reach the callback
        $response = $this->bulk(['bulkActionIds' => ['2', '999'], 'bulkAction' => 'activate']);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $html = $this->followRedirect($response, 'Admin:Vehicle', ['action' => 'default']);
        $this->assertStringContainsString('(2)', $html);
        $this->assertStringNotContainsString('999)', $html);
        $this->assertStringNotContainsString(', 999', $html);
    }

    /**
     * @param array<string, mixed> $post
     */
    private function bulk(array $post): \Nette\Application\Response
    {
        return $this->submitForm('Admin:Vehicle', self::BULK_FORM, $post + ['bulkActionSubmit' => 'Provést'], ['action' => 'default']);
    }
}
