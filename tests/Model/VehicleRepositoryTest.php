<?php declare(strict_types=1);

namespace Tests\Model;

use App\Model\PageRequest;
use App\Model\SortDirection;
use App\Model\Vehicle\VehicleEntity;
use App\Model\Vehicle\VehicleFilter;
use App\Model\Vehicle\VehicleRepository;
use App\Model\Vehicle\VehicleSortBy;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\DatabaseTestCase;

#[CoversClass(VehicleRepository::class)]
final class VehicleRepositoryTest extends DatabaseTestCase
{
    private VehicleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::container()->getByType(VehicleRepository::class);
    }

    public function testFindPaginatesTheMockData(): void
    {
        $total = $this->repository->count();

        $first = $this->repository->find(new VehicleFilter(), new PageRequest(1, 10));
        $this->assertSame($total, $first->totalCount);
        $this->assertCount(10, $first->entities);
        $this->assertSame((int) ceil($total / 10), $first->pageCount);

        $last = $this->repository->find(new VehicleFilter(), new PageRequest($first->pageCount, 10));
        $this->assertCount($total - 10 * ($first->pageCount - 1), $last->entities);
    }

    public function testFindWithoutPageRequestReturnsEverything(): void
    {
        $list = $this->repository->find(new VehicleFilter(), null);

        $this->assertCount($this->repository->count(), $list->entities);
    }

    public function testFindFiltersByRentalOffice(): void
    {
        $list = $this->repository->find(new VehicleFilter(rentalOffice: 1), null);

        $this->assertNotEmpty($list->entities);
        foreach ($list->entities as $vehicle) {
            $this->assertSame(1, $vehicle->rentalOfficeId);
        }
    }

    public function testFindFiltersByPriceRange(): void
    {
        // prices far outside the mock data (360 - 1040 Kč per day), so only these vehicles can match
        $this->insertVehicle('CHEAP', 1.0);
        $this->insertVehicle('MIDDLE', 7777.0);
        $this->insertVehicle('PRICEY', 99999.0);

        $list = $this->repository->find(new VehicleFilter(costPerDayFrom: 5000.0, costPerDayTo: 50000.0), null);

        $this->assertSame(['MIDDLE'], array_map(fn(VehicleEntity $v) => $v->model, $list->entities));
    }

    public function testFindSortsByPriceInBothDirections(): void
    {
        $this->insertVehicle('CHEAP', 1.0);
        $this->insertVehicle('PRICEY', 99999.0);

        $descending = $this->repository->find(
            new VehicleFilter(sortBy: VehicleSortBy::CostPerDay, sortDirection: SortDirection::Descending),
            new PageRequest(1, 1),
        );
        $ascending = $this->repository->find(
            new VehicleFilter(sortBy: VehicleSortBy::CostPerDay, sortDirection: SortDirection::Ascending),
            new PageRequest(1, 1),
        );

        $this->assertSame('PRICEY', $descending->entities[0]->model);
        $this->assertSame('CHEAP', $ascending->entities[0]->model);
    }

    public function testTheMockVehiclesHaveRealisticPrices(): void
    {
        $vehicles = $this->repository->find(new VehicleFilter(), null);

        foreach ($vehicles as $vehicle) {
            $this->assertGreaterThan(0.0, $vehicle->costPerDay, "vehicle #{$vehicle->id} has no daily price");
            $this->assertGreaterThan(0.0, $vehicle->costPerUnit, "vehicle #{$vehicle->id} has no price per km");
            $this->assertLessThan($vehicle->costPerDay, $vehicle->costPerUnit, 'a kilometer costs less than a day');
        }
    }

    public function testPriceFilterNarrowsTheMockData(): void
    {
        $all = $this->repository->find(new VehicleFilter(), null);

        $expensive = $this->repository->find(new VehicleFilter(costPerDayFrom: 800.0), null);

        $this->assertNotEmpty($expensive->entities, 'the mock data has vehicles above 800 Kč per day');
        $this->assertLessThan($all->getCount(), $expensive->getCount());
        foreach ($expensive as $vehicle) {
            $this->assertGreaterThanOrEqual(800.0, $vehicle->costPerDay);
        }
    }

    public function testPricePerKilometerCanBeFilteredToo(): void
    {
        $list = $this->repository->find(new VehicleFilter(costPerUnitFrom: 5.0, costPerUnitTo: 6.0), null);

        $this->assertNotEmpty($list->entities);
        foreach ($list as $vehicle) {
            $this->assertGreaterThanOrEqual(5.0, $vehicle->costPerUnit);
            $this->assertLessThanOrEqual(6.0, $vehicle->costPerUnit);
        }
    }

    private function insertVehicle(string $model, float $costPerDay): void
    {
        $this->repository->insert(new VehicleEntity(
            id: null,
            rentalOfficeId: 1,
            typeId: 1,
            colorId: 1,
            fuelTypeId: 1,
            model: $model,
            plateNumber: 'T' . substr(md5($model), 0, 6),
            year: 2020,
            costPerDay: $costPerDay,
        ));
    }
}
