<?php declare(strict_types=1);

namespace Tests\Model;

use App\Model\CarRental\CarRentalEntity;
use App\Model\CarRental\CarRentalFilter;
use App\Model\CarRental\CarRentalRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\DatabaseTestCase;

#[CoversClass(CarRentalRepository::class)]
final class CarRentalRepositoryTest extends DatabaseTestCase
{
    private CarRentalRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::container()->getByType(CarRentalRepository::class);
    }

    public function testFilterByVehicle(): void
    {
        $list = $this->repository->find(new CarRentalFilter(vehicleId: 1), null);

        $this->assertNotEmpty($list->entities);
        foreach ($list->entities as $rental) {
            $this->assertSame(1, $rental->vehicleId);
        }
    }

    public function testActiveOnlyKeepsReservationsAndRentalsInProgress(): void
    {
        $active = 0;
        foreach (range(1, 25) as $vehicleId) {
            $list = $this->repository->find(new CarRentalFilter(vehicleId: $vehicleId, activeOnly: true), null);
            foreach ($list->entities as $rental) {
                $inProgress = $rental->startAt !== null && $rental->endAt === null;
                $openReservation = $rental->startAt === null && !$rental->deleted;
                $this->assertTrue($inProgress || $openReservation, "rental #{$rental->id} is finished or deleted");
                $active++;
            }
        }
        $this->assertGreaterThan(0, $active, 'the mock data has rentals in progress');
    }

    public function testActiveOnlyReturnsFewerRentalsThanAll(): void
    {
        $all = $this->repository->find(new CarRentalFilter(vehicleId: 1, activeOnly: false), null);
        $active = $this->repository->find(new CarRentalFilter(vehicleId: 1, activeOnly: true), null);

        $this->assertLessThan($all->getCount(), $active->getCount());
    }

    public function testRelatedEntitiesAreLoaded(): void
    {
        $rental = $this->repository->find(new CarRentalFilter(vehicleId: 1), null)->getFirst();

        $this->assertNotNull($rental);
        $this->assertSame(1, $rental->vehicle?->id);
        $this->assertNotNull($rental->customer);
        $this->assertNotNull($rental->rentalOffice);
    }

    public function testReservationRoundTrip(): void
    {
        $rental = new CarRentalEntity(
            id: null,
            vehicleId: 2,
            rentalOfficeId: 3,
            customerId: 4,
            borrowerId: null,
            recipientId: null,
            reservedAt: new \DateTime('2030-01-01 10:30:00'),
            reservedFrom: new \DateTime('2030-01-10 00:00:00'),
            reservedTo: new \DateTime('2030-01-12 23:59:59'),
        );

        $this->repository->insert($rental);
        $loaded = $this->repository->get((int) $rental->id);

        $this->assertNotNull($loaded);
        $this->assertSame(2, $loaded->vehicleId);
        $this->assertSame(3, $loaded->rentalOfficeId);
        $this->assertSame(4, $loaded->customerId);
        $this->assertNull($loaded->borrowerId);
        $this->assertNull($loaded->startAt);
        $this->assertNull($loaded->endAt);
        $this->assertFalse($loaded->deleted);
        $this->assertSame('2030-01-01 10:30:00', $loaded->reservedAt->format('Y-m-d H:i:s'));
        $this->assertSame('2030-01-10', $loaded->reservedFrom->format('Y-m-d'));
        $this->assertSame('2030-01-12', $loaded->reservedTo->format('Y-m-d'));
    }
}
