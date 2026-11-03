<?php declare(strict_types=1);

namespace Tests\Model;

use App\Model\Customer\CustomerEntity;
use App\Model\Customer\CustomerRepository;
use Nette\Database\UniqueConstraintViolationException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\DatabaseTestCase;

#[CoversClass(CustomerRepository::class)]
final class CustomerRepositoryTest extends DatabaseTestCase
{
    private CustomerRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::container()->getByType(CustomerRepository::class);
    }

    public function testGetByEmailFindsTheMockCustomer(): void
    {
        $customer = $this->repository->getByEmail('bilbo.baggins@example.com');

        $this->assertNotNull($customer);
        $this->assertSame('Bilbo', $customer->firstname);
    }

    public function testGetByEmailReturnsNullForUnknownEmail(): void
    {
        $this->assertNull($this->repository->getByEmail('nobody@example.com'));
    }

    public function testInsertAssignsAnId(): void
    {
        $customer = $this->newCustomer('new@example.com');

        $this->repository->insert($customer);

        $this->assertNotNull($customer->id);
        $this->assertSame($customer->id, $this->repository->getByEmail('new@example.com')?->id);
    }

    public function testEmailMustBeUnique(): void
    {
        $this->repository->insert($this->newCustomer('same@example.com'));

        $this->expectException(UniqueConstraintViolationException::class);
        $this->repository->insert($this->newCustomer('same@example.com'));
    }

    public function testUpdatePersistsChanges(): void
    {
        $customer = $this->newCustomer('change@example.com');
        $this->repository->insert($customer);

        $customer->city = 'Brno';
        $this->repository->update($customer);

        $this->assertSame('Brno', $this->repository->get((int) $customer->id)?->city);
    }

    private function newCustomer(string $email): CustomerEntity
    {
        return new CustomerEntity(
            id: null,
            password: 'x',
            email: $email,
            firstname: 'Test',
            surname: 'User',
            identityNumber: '123456789',
            phone: '123456789',
            street: 'Street 1',
            city: 'Praha',
            zip: '11000',
            country: 'cz',
        );
    }
}
