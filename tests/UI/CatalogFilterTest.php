<?php declare(strict_types=1);

namespace Tests\UI;

use Nette\Application\Response;
use Nette\Application\Responses\RedirectResponse;
use Tests\PresenterTestCase;

final class CatalogFilterTest extends PresenterTestCase
{
    private const ITEMS_PER_PAGE = 8;

    /**
     * @param array<string, mixed> $params
     */
    private function catalog(array $params = []): string
    {
        return $this->renderPage($this->runPresenter('Front:Catalog', ['action' => 'default'] + $params));
    }

    private function itemsOn(string $html): int
    {
        return substr_count($html, '<div class="item">');
    }

    /**
     * @param array<string, string> $overrides
     * @param string $button the name the browser posts: Nette prefixes names that clash with DOM properties
     *                       (a button called "reset" is posted as "_reset")
     */
    private function filter(array $overrides = [], string $button = 'filter'): Response
    {
        return $this->submitForm('Front:Catalog', 'filterForm', $overrides + [
            'sortBy' => 'vehicle_name',
            'sortDirection' => 'asc',
            $button => 'x',
        ], ['action' => 'default']);
    }

    public function testTheFirstPageShowsOnePageOfVehicles(): void
    {
        $this->assertSame(self::ITEMS_PER_PAGE, $this->itemsOn($this->catalog()));
    }

    public function testTheFilterNarrowsTheListToOneRentalOffice(): void
    {
        // the mock data has 5 vehicles in the first rental office
        $this->assertSame(5, $this->itemsOn($this->catalog(['rentalOffice' => 1])));
    }

    public function testAnImpossiblePriceRangeFindsNothing(): void
    {
        $html = $this->catalog(['costPerDayFrom' => 1000000]);

        $this->assertSame(0, $this->itemsOn($html));
        $this->assertStringContainsString('Nebyla nalezena žádná vozidla!', $html);
    }

    public function testAnUnknownSortColumnFallsBackToTheDefaultOrder(): void
    {
        $this->assertSame($this->itemsOn($this->catalog()), $this->itemsOn($this->catalog(['sortBy' => 'password; DROP TABLE vehicle'])));
    }

    public function testSubmittingTheFilterRedirectsWithTheChosenValuesInTheUrl(): void
    {
        $response = $this->filter(['rentalOffice' => '2', 'costPerDayFrom' => '100', 'sortDirection' => 'desc']);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        parse_str((string) parse_url($response->getUrl(), PHP_URL_QUERY), $query);
        $this->assertSame('2', $query['rentalOffice'] ?? null);
        $this->assertSame('100', $query['costPerDayFrom'] ?? null);
        $this->assertSame('desc', $query['sortDirection'] ?? null);
        $this->assertArrayNotHasKey('color', $query, 'empty filters stay out of the URL');
        $this->assertArrayNotHasKey('fuelType', $query);
    }

    public function testResetClearsTheFilter(): void
    {
        $response = $this->filter(['rentalOffice' => '2'], button: '_reset');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringNotContainsString('rentalOffice', $response->getUrl());
    }

    public function testPageZeroGoesBackToThePageOne(): void
    {
        $response = $this->runPresenter('Front:Catalog', ['action' => 'default', 'page' => 0]);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringNotContainsString('page=0', $response->getUrl());
    }

    public function testAPageBeyondTheLastGoesToTheLastPage(): void
    {
        $response = $this->runPresenter('Front:Catalog', ['action' => 'default', 'page' => 99]);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('page=4', $response->getUrl(), '25 vehicles in pages of 8 make 4 pages');
    }

    public function testTheLastPageShowsTheRest(): void
    {
        $this->assertSame(1, $this->itemsOn($this->catalog(['page' => 4])));
    }
}
