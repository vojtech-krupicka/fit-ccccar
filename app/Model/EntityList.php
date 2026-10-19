<?php declare(strict_types=1);

namespace App\Model;

/**
 * @template T of BaseEntity
 * @implements \IteratorAggregate<int, T>
 */
readonly class EntityList implements \IteratorAggregate, \Countable
{
    /**
     * @param list<T> $entities
     * @param int $totalCount
     * @param int|null $currentPage
     * @param int|null $pageCount
     */
    public function __construct(
        public readonly array $entities,
        public readonly int $totalCount,
        public readonly int|null $currentPage = null,
        public readonly int|null $pageCount = null,
    ) {}

    #region Implementation of interfaces

    /**
     * @return \ArrayIterator<int, T>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->entities);
    }

    /**
     * Number of entities in this list (this page), not the database total.
     */
    public function count(): int
    {
        return count($this->entities);
    }

    #endregion

    #region Entity access methods

    public function isEmpty(): bool
    {
        return $this->entities === [];
    }

    /**
     * @return T|null
     */
    public function getFirst(): ?BaseEntity
    {
        return $this->entities[0] ?? null;
    }

    /**
     * @return T|null
     */
    public function getLast(): ?BaseEntity
    {
        return $this->entities === [] ? null : $this->entities[array_key_last($this->entities)];
    }

    #endregion

    #region Conversion and higher-order functions

    /**
     * @return list<T>
     */
    public function toArray(): array
    {
        return $this->entities;
    }

    /**
     * Keeps entities for which the callback returns true. The result is no longer a database page,
     * so totalCount is the new size and the paging info is dropped.
     *
     * @param callable(T): bool $predicate
     * @return self<T>
     */
    public function filter(callable $predicate): self
    {
        $entities = array_values(array_filter($this->entities, $predicate));
        return new self($entities, count($entities));
    }

    /**
     * @template U
     * @param callable(T): U $callback
     * @return list<U>
     */
    public function map(callable $callback): array
    {
        return array_map($callback, $this->entities);
    }

    /**
     * @template K of int|string
     * @param callable(T): K $key
     * @return array<K, T>
     */
    public function indexBy(callable $key): array
    {
        $result = [];
        foreach ($this->entities as $entity) {
            $result[$key($entity)] = $entity;
        }
        return $result;
    }

    /**
     * Builds a key => value map, for example for select boxes.
     *
     * @template K of int|string
     * @template V
     * @param callable(T): K $key
     * @param callable(T): V $value
     * @return array<K, V>
     */
    public function toPairs(callable $key, callable $value): array
    {
        $result = [];
        foreach ($this->entities as $entity) {
            $result[$key($entity)] = $value($entity);
        }
        return $result;
    }

    #endregion
}
