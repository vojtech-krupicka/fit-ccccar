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
        $this->insertVehicle('CHEAP', 100.0);
        $this->insertVehicle('MIDDLE', 200.0);
        $this->insertVehicle('PRICEY', 300.0);

        $list = $this->repository->find(new VehicleFilter(costPerDayFrom: 150.0, costPerDayTo: 250.0), null);

        $this->assertSame(['MIDDLE'], array_map(fn(VehicleEntity $v) => $v->model, $list->entities));
    }

    public function testFindSortsByPriceInBothDirections(): void
    {
        $this->insertVehicle('CHEAP', 100.0);
        $this->insertVehicle('PRICEY', 300.0);

        $descending = $this->repository->find(
            new VehicleFilter(sortBy: VehicleSortBy::CostPerDay, sortDirection: SortDirection::Descending),
            new PageRequest(1, 1),
        );
        $ascending = $this->repository->find(
            new VehicleFilter(sortBy: VehicleSortBy::CostPerDay, sortDirection: SortDirection::Ascending),
            new PageRequest(1, 1),
        );

        $this->assertSame('PRICEY', $descending->entities[0]->model);
        $this->assertSame(0.0, $ascending->entities[0]->costPerDay, 'the mock data is free, so it sorts first');
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
