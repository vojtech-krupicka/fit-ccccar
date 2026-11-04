<?php declare(strict_types=1);

namespace Tests\Model;

use App\Model\CarRental\CarRentalFilter;
use App\Model\CarRental\CarRentalRepository;
use App\Model\CarRental\CarRentalSortBy;
use App\Model\Customer\CustomerFilter;
use App\Model\Customer\CustomerRepository;
use App\Model\Customer\CustomerSortBy;
use App\Model\PageRequest;
use App\Model\RentalOffice\RentalOfficeFilter;
use App\Model\RentalOffice\RentalOfficeRepository;
use App\Model\RentalOffice\RentalOfficeSortBy;
use App\Model\SortDirection;
use App\Model\Vehicle\VehicleFilter;
use App\Model\Vehicle\VehicleRepository;
use App\Model\Vehicle\VehicleSortBy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\DatabaseTestCase;

/**
 * Every sort option is a separate SQL fragment (some with joins like "borrower.name"),
 * so each one has to be run at least once: a typo only shows up as an SQL error at runtime.
 */
#[CoversClass(CarRentalRepository::class)]
#[CoversClass(CustomerRepository::class)]
#[CoversClass(RentalOfficeRepository::class)]
#[CoversClass(VehicleRepository::class)]
final class RepositorySortingTest extends DatabaseTestCase
{
    /**
     * @return iterable<string, array{object, SortDirection}>
     */
    private static function everyCase(string $enum): iterable
    {
        foreach ($enum::cases() as $case) {
            foreach (SortDirection::cases() as $direction) {
                yield "{$case->name} {$direction->name}" => [$case, $direction];
            }
        }
    }

    /** @return iterable<string, array{object, SortDirection}> */
    public static function carRentalSorts(): iterable
    {
        return self::everyCase(CarRentalSortBy::class);
    }

    /** @return iterable<string, array{object, SortDirection}> */
    public static function customerSorts(): iterable
    {
        return self::everyCase(CustomerSortBy::class);
    }

    /** @return iterable<string, array{object, SortDirection}> */
    public static function rentalOfficeSorts(): iterable
    {
        return self::everyCase(RentalOfficeSortBy::class);
    }

    /** @return iterable<string, array{object, SortDirection}> */
    public static function vehicleSorts(): iterable
    {
        return self::everyCase(VehicleSortBy::class);
    }

    #[DataProvider('carRentalSorts')]
    public function testEveryCarRentalSortReturnsAllRows(CarRentalSortBy $sortBy, SortDirection $direction): void
    {
        $repository = self::container()->getByType(CarRentalRepository::class);

        $list = $repository->find(new CarRentalFilter(sortBy: $sortBy, sortDirection: $direction), null);

        $this->assertSame($repository->count(), $list->getCount(), 'sorting must not lose or duplicate rows (a join could)');
    }

    #[DataProvider('customerSorts')]
    public function testEveryCustomerSortReturnsAllRows(CustomerSortBy $sortBy, SortDirection $direction): void
    {
        $repository = self::container()->getByType(CustomerRepository::class);

        $list = $repository->find(new CustomerFilter(sortBy: $sortBy, sortDirection: $direction), null);

        $this->assertSame($repository->count(), $list->getCount());
    }

    #[DataProvider('rentalOfficeSorts')]
    public function testEveryRentalOfficeSortReturnsAllRows(RentalOfficeSortBy $sortBy, SortDirection $direction): void
    {
        $repository = self::container()->getByType(RentalOfficeRepository::class);

        $list = $repository->find(new RentalOfficeFilter(sortBy: $sortBy, sortDirection: $direction), null);

        $this->assertSame($repository->count(), $list->getCount());
    }

    #[DataProvider('vehicleSorts')]
    public function testEveryVehicleSortReturnsAllRows(VehicleSortBy $sortBy, SortDirection $direction): void
    {
        $repository = self::container()->getByType(VehicleRepository::class);

        $list = $repository->find(new VehicleFilter(sortBy: $sortBy, sortDirection: $direction), null);

        $this->assertSame($repository->count(), $list->getCount());
    }

    public function testCarRentalsCanBeSortedByNewestFirst(): void
    {
        $list = self::container()->getByType(CarRentalRepository::class)
            ->find(new CarRentalFilter(sortBy: CarRentalSortBy::Latest), null);

        $times = $list->map(fn($rental) => $rental->reservedAt->getTimestamp());
        $sorted = $times;
        rsort($sorted);
        $this->assertSame($sorted, $times);
    }

    public function testCarRentalsCanBeSortedByOldestFirst(): void
    {
        $list = self::container()->getByType(CarRentalRepository::class)
            ->find(new CarRentalFilter(sortBy: CarRentalSortBy::Oldest), null);

        $times = $list->map(fn($rental) => $rental->reservedAt->getTimestamp());
        $sorted = $times;
        sort($sorted);
        $this->assertSame($sorted, $times);
    }

    public function testCustomersSortedByIdDescending(): void
    {
        $list = self::container()->getByType(CustomerRepository::class)
            ->find(new CustomerFilter(CustomerSortBy::Id, SortDirection::Descending), null);

        $ids = $list->map(fn($customer) => $customer->id);
        $sorted = $ids;
        rsort($sorted);
        $this->assertSame($sorted, $ids);
    }

    public function testCustomersSortedByCreationDate(): void
    {
        $list = self::container()->getByType(CustomerRepository::class)
            ->find(new CustomerFilter(CustomerSortBy::Created, SortDirection::Ascending), null);

        $times = $list->map(fn($customer) => $customer->created->getTimestamp());
        $sorted = $times;
        sort($sorted);
        $this->assertSame($sorted, $times);
    }

    public function testPagingAndSortingTogether(): void
    {
        $repository = self::container()->getByType(CustomerRepository::class);
        $filter = new CustomerFilter(CustomerSortBy::Id, SortDirection::Ascending);

        $first = $repository->find($filter, new PageRequest(1, 10));
        $second = $repository->find($filter, new PageRequest(2, 10));

        $this->assertSame(1, $first->getFirst()?->id);
        $this->assertSame(11, $second->getFirst()?->id);
        $this->assertSame([], array_intersect($first->map(fn($c) => $c->id), $second->map(fn($c) => $c->id)));
    }

    public function testAPageBeyondTheLastIsEmptyButKeepsTheTotal(): void
    {
        $repository = self::container()->getByType(CustomerRepository::class);

        $list = $repository->find(new CustomerFilter(), new PageRequest(99, 10));

        $this->assertTrue($list->isEmpty());
        $this->assertSame($repository->count(), $list->getTotalCount());
    }
}
