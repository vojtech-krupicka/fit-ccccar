<?php declare(strict_types=1);

namespace Tests\Model;

use App\Model\Customer\CustomerEntity;
use App\Model\Customer\CustomerRepository;
use App\Model\Employee\EmployeeEntity;
use App\Model\Employee\EmployeeRepository;
use App\Model\RentalOffice\RentalOfficeEntity;
use App\Model\RentalOffice\RentalOfficeRepository;
use App\Model\Vehicle\VehicleEntity;
use App\Model\Vehicle\VehicleRepository;
use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\DatabaseTestCase;

/**
 * Entity -> database -> entity: what goes in must come out, including NULLs and related entities.
 */
#[CoversNothing]
final class MapperTest extends DatabaseTestCase
{
    public function testRentalOfficeWithoutSupervisorKeepsNull(): void
    {
        $repository = self::container()->getByType(RentalOfficeRepository::class);
        $office = new RentalOfficeEntity(null, null, 'Test office', 'Street 1', 'Praha', '11000', 'cz', '123456789', 'test@example.com', 50.0875, 14.4214);

        $repository->insert($office);
        $loaded = $repository->get((int) $office->id);

        $this->assertNotNull($loaded);
        $this->assertNull($loaded->supervisorId, 'NULL in the database must not become 0');
        $this->assertNull($loaded->supervisor);
        $this->assertTrue($loaded->employeeList?->isEmpty());
        $this->assertSame('Test office', $loaded->name);
        $this->assertEqualsWithDelta(50.0875, $loaded->latitude, 0.00001);
        $this->assertEqualsWithDelta(14.4214, $loaded->longitude, 0.00001);
    }

    public function testRentalOfficeLoadsItsEmployees(): void
    {
        $office = self::container()->getByType(RentalOfficeRepository::class)->get(1);

        $this->assertNotNull($office);
        $this->assertNotNull($office->employeeList);
        $this->assertNotEmpty($office->employeeList->entities);
        foreach ($office->employeeList as $employee) {
            $this->assertSame(1, $employee->rentalOfficeId);
        }
    }

    public function testRentalOfficeWithASupervisor(): void
    {
        $repository = self::container()->getByType(RentalOfficeRepository::class);
        $office = $repository->get(1);
        $this->assertNotNull($office);
        $office->supervisorId = 1;
        $repository->update($office);

        $loaded = $repository->get(1);

        $this->assertSame(1, $loaded?->supervisorId);
        $this->assertSame(1, $loaded->supervisor?->id);
    }

    public function testVehicleRoundTrip(): void
    {
        $repository = self::container()->getByType(VehicleRepository::class);
        $vehicle = new VehicleEntity(
            id: null,
            rentalOfficeId: 2,
            typeId: 3,
            colorId: 4,
            fuelTypeId: 2,
            model: '2.0 TDI',
            plateNumber: 'AB12345',
            mileage: 1234.5,
            year: 2021,
            costPerDay: 999.5,
            costPerUnit: 12.25,
        );

        $repository->insert($vehicle);
        $loaded = $repository->get((int) $vehicle->id);

        $this->assertNotNull($loaded);
        $this->assertSame([2, 3, 4, 2], [$loaded->rentalOfficeId, $loaded->typeId, $loaded->colorId, $loaded->fuelTypeId]);
        $this->assertSame('2.0 TDI', $loaded->model);
        $this->assertSame('AB12345', $loaded->plateNumber);
        $this->assertSame(1234.5, $loaded->mileage);
        $this->assertSame(2021, $loaded->year);
        $this->assertSame(999.5, $loaded->costPerDay);
        $this->assertSame(12.25, $loaded->costPerUnit);
        // related entities come with it
        $this->assertSame(2, $loaded->rentalOffice?->id);
        $this->assertSame(3, $loaded->type?->id);
        $this->assertSame(4, $loaded->color?->id);
        $this->assertSame(2, $loaded->fuelType?->id);
        $this->assertNotSame('', $loaded->getFullName());
    }

    public function testCustomerRoundTrip(): void
    {
        $repository = self::container()->getByType(CustomerRepository::class);
        $customer = new CustomerEntity(null, 'hash', 'round@example.com', 'Jan', 'Novák', '987654321', '+420123456789', 'Ulice 5', 'Brno', '60200', 'sk');

        $repository->insert($customer);
        $loaded = $repository->get((int) $customer->id);

        $this->assertNotNull($loaded);
        $this->assertSame(
            ['hash', 'round@example.com', 'Jan', 'Novák', '987654321', '+420123456789', 'Ulice 5', 'Brno', '60200', 'sk', 'Jan Novák'],
            [$loaded->password, $loaded->email, $loaded->firstname, $loaded->surname, $loaded->identityNumber, $loaded->phone, $loaded->street, $loaded->city, $loaded->zip, $loaded->country, $loaded->fullname],
        );
    }

    public function testEmployeeRoundTrip(): void
    {
        $repository = self::container()->getByType(EmployeeRepository::class);
        $employee = new EmployeeEntity(null, 2, 'hash', 'staff@example.com', 'Eva', 'Dvořáková', '123456789');

        $repository->insert($employee);
        $loaded = $repository->get((int) $employee->id);

        $this->assertNotNull($loaded);
        $this->assertSame(2, $loaded->rentalOfficeId);
        $this->assertSame('staff@example.com', $loaded->email);
        $this->assertSame('Eva Dvořáková', $loaded->fullname);
    }

    public function testLoadingNothingGivesNull(): void
    {
        $this->assertNull(self::container()->getByType(CustomerRepository::class)->get(999999));
        $this->assertNull(self::container()->getByType(VehicleRepository::class)->get(999999));
    }
}
