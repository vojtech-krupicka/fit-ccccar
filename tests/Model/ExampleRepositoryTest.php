<?php declare(strict_types=1);

namespace Tests\Model;

use App\Model\Example\ExampleEntity;
use App\Model\Example\ExampleRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\DatabaseTestCase;

#[CoversClass(ExampleRepository::class)]
final class ExampleRepositoryTest extends DatabaseTestCase
{
    private ExampleRepository $facade;

    protected function setUp(): void
    {
        parent::setUp();
        $this->facade = self::container()->getByType(ExampleRepository::class);
    }

    public function testGetAllReturnsTheMockDataSortedByName(): void
    {
        $names = array_map(fn(ExampleEntity $e) => $e->name, $this->facade->getAll());

        $this->assertSame(['Alpha', 'Beta'], $names);
    }

    public function testGetById(): void
    {
        $item = $this->facade->getById(1);

        $this->assertNotNull($item);
        $this->assertSame('Alpha', $item->name);
        $this->assertNull($this->facade->getById(9999));
    }

    public function testPersistInsertsAndUpdates(): void
    {
        $item = new ExampleEntity(name: 'Gamma');
        $this->facade->persist($item);
        $id = $item->id;
        $this->assertNotNull($id, 'persist() assigns the id of a new row');

        $item->name = 'Gamma 2';
        $this->facade->persist($item);

        $this->assertSame('Gamma 2', $this->facade->getById($id)?->name);
    }

    public function testDelete(): void
    {
        $item = $this->facade->getById(2);
        $this->assertNotNull($item);

        $this->facade->delete($item);

        $this->assertNull($this->facade->getById(2));
    }

    public function testChangesAreRolledBackBetweenTests(): void
    {
        // testDelete removed item 2 inside its own transaction
        $this->assertCount(2, $this->facade->getAll());
    }
}
