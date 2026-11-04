<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Actions;

class BulkAction
{
    /**
     * @param \Closure(list<int>): void $callback
     */
    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly \Closure $callback,
        public ?string $confirmMsg = null,
    ) {}

    /**
     * Executes the bulk action callback with the given entity IDs.
     *
     * @param list<int> $ids
     */
    public function execute(array $ids): void
    {
        ($this->callback)($ids);
    }

    public function setConfirmMsg(string $confirmMsg): static
    {
        $this->confirmMsg = $confirmMsg;
        return $this;
    }
}
