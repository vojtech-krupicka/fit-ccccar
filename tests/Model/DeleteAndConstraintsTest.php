<?php declare(strict_types=1);

namespace Tests\Model;

use App\Model\Customer\CustomerFacade;
use App\Model\Employee\EmployeeEntity;
use App\Model\Employee\EmployeeRepository;
use App\Model\PageRequest;
use App\Model\RentalOffice\RentalOfficeRepository;
use App\Model\Vehicle\VehicleRepository;
use Nette\Database\Explorer;
use Nette\Database\ForeignKeyConstraintViolationException;
use Nette\Database\UniqueConstraintViolationException;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Model\BaseRepository;
use Tests\CustomerFixture;
use Tests\DatabaseTestCase;

#[CoversClass(BaseRepository::class)]
final class DeleteAndConstraintsTest extends DatabaseTestCase
{
    use CustomerFixture;

    public function testDeletingAnUnusedCustomerSucceeds(): void
    {
        $facade = self::container()->getByType(CustomerFacade::class);
        $customer = $this->createCustomer('delete@example.com');

        $this->assertTrue($facade->delete($customer));

        $this->assertNull($facade->get((int) $customer->id));
    }

    public function testDeletingAMissingRowReportsFalse(): void
    {
        $this->assertFalse(self::container()->getByType(VehicleRepository::class)->delete(999999));
    }

    public function testAnEntityThatWasNeverSavedIsNotDeleted(): void
    {
        $customer = new \App\Model\Customer\CustomerEntity(null, 'x');

        $this->assertFalse(self::container()->getByType(CustomerFacade::class)->delete($customer));
    }

    public function testAVehicleWithRentalsCannotBeDeleted(): void
    {
        $vehicleId = (int) self::container()->getByType(Explorer::class)->table('car_rental')->fetch()['vehicle_id'];

        $this->expectException(ForeignKeyConstraintViolationException::class);
        self::container()->getByType(VehicleRepository::class)->delete($vehicleId);
    }

    public function testARentalOfficeWithEmployeesCannotBeDeleted(): void
    {
        $this->expectException(ForeignKeyConstraintViolationException::class);
        self::container()->getByType(RentalOfficeRepository::class)->delete(1);
    }

    public function testEmployeeEmailMustBeUnique(): void
    {
        $repository = self::container()->getByType(EmployeeRepository::class);
        $existing = $repository->get(1);
        $this->assertNotNull($existing);

        $this->expectException(UniqueConstraintViolationException::class);
        $repository->insert(new EmployeeEntity(null, 1, 'hash', $existing->email, 'Dup', 'Licate', '123456789'));
    }

    public function testGetAllIsOrderedByThePrimaryKey(): void
    {
        $ids = self::container()->getByType(VehicleRepository::class)->getAll()->map(fn($v) => $v->id);

        $sorted = $ids;
        sort($sorted);
        $this->assertSame($sorted, $ids);
    }

    public function testGetAllPagesTheResult(): void
    {
        $repository = self::container()->getByType(VehicleRepository::class);

        $page = $repository->getAll(new PageRequest(2, 10));

        $this->assertCount(10, $page->entities);
        $this->assertSame($repository->count(), $page->totalCount);
        $this->assertSame(11, $page->getFirst()?->id);
        $this->assertSame(2, $page->currentPage);
    }
}
