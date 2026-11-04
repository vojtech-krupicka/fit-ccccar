<?php declare(strict_types=1);

namespace Tests\Model;

use App\Model\BaseEntity;
use App\Model\EntityList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EntityList::class)]
final class EntityListTest extends TestCase
{
    /**
     * @param list<int> $ids
     * @return EntityList<BaseEntity>
     */
    private function list(array $ids, ?int $total = null, ?int $page = null, ?int $perPage = null): EntityList
    {
        $entities = array_map(fn(int $id) => new class($id) extends BaseEntity {
            public function __construct(?int $id)
            {
                $this->id = $id;
            }
        }, $ids);

        return new EntityList($entities, $total ?? count($ids), $page, $page === null ? null : (int) ceil(($total ?? count($ids)) / ($perPage ?? 10)), $perPage);
    }

    public function testIndexesOfAMiddlePage(): void
    {
        $list = $this->list(range(11, 20), total: 25, page: 2, perPage: 10);

        $this->assertSame(11, $list->getMinIndex());
        $this->assertSame(20, $list->getMaxIndex());
        $this->assertSame(25, $list->getTotalCount());
    }

    public function testTheLastPageEndsAtTheTotal(): void
    {
        $list = $this->list(range(21, 25), total: 25, page: 3, perPage: 10);

        $this->assertSame(21, $list->getMinIndex());
        $this->assertSame(25, $list->getMaxIndex(), 'the last page is shorter than a full page');
    }

    public function testAnUnpagedListCoversEverything(): void
    {
        $list = $this->list([1, 2, 3]);

        $this->assertSame(1, $list->getMinIndex());
        $this->assertSame(3, $list->getMaxIndex());
    }

    public function testCountIsTheNumberOfEntitiesOnThePage(): void
    {
        $list = $this->list(range(1, 10), total: 25, page: 1, perPage: 10);

        $this->assertCount(10, $list);
        $this->assertSame(10, $list->getCount());
        $this->assertSame(25, $list->getTotalCount());
    }

    public function testEmptyList(): void
    {
        $list = $this->list([]);

        $this->assertTrue($list->isEmpty());
        $this->assertNull($list->getFirst());
        $this->assertNull($list->getLast());
        $this->assertSame([], $list->toArray());
    }

    public function testFirstLastAndIteration(): void
    {
        $list = $this->list([5, 6, 7]);

        $this->assertFalse($list->isEmpty());
        $this->assertSame(5, $list->getFirst()?->id);
        $this->assertSame(7, $list->getLast()?->id);
        $this->assertSame([5, 6, 7], array_map(fn(BaseEntity $e) => $e->id, iterator_to_array($list)));
    }

    public function testFilterKeepsMatchingEntitiesAndDropsThePagingInfo(): void
    {
        $list = $this->list(range(1, 10), total: 25, page: 1, perPage: 10);

        $even = $list->filter(fn(BaseEntity $e) => $e->id % 2 === 0);

        $this->assertSame([2, 4, 6, 8, 10], array_map(fn(BaseEntity $e) => $e->id, $even->toArray()));
        $this->assertSame(5, $even->getTotalCount(), 'a filtered list is no longer a database page');
        $this->assertNull($even->getCurrentPage());
    }

    public function testFilterReindexesTheList(): void
    {
        $filtered = $this->list([1, 2, 3])->filter(fn(BaseEntity $e) => $e->id !== 1);

        $this->assertSame([0, 1], array_keys($filtered->toArray()));
    }

    public function testMapReturnsAListOfTheResults(): void
    {
        $this->assertSame([2, 4, 6], $this->list([1, 2, 3])->map(fn(BaseEntity $e) => $e->id * 2));
    }

    public function testIndexByAndToPairs(): void
    {
        $list = $this->list([10, 20]);

        $this->assertSame([10, 20], array_keys($list->indexBy(fn(BaseEntity $e) => $e->id)));
        $this->assertSame([10 => 'id10', 20 => 'id20'], $list->toPairs(fn(BaseEntity $e) => $e->id, fn(BaseEntity $e) => "id{$e->id}"));
    }
}
