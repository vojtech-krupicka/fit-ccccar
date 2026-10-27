<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\NewDatagrid;

use App\Model\BaseEntity;
use App\Model\EntityList;
use App\UI\Shared\Components\Datagrids\NewDatagrid\Types\ActionSeverity;
use App\UI\Shared\Components\Datagrids\NewDatagrid\Types\ColumnWidth;
use App\UI\Shared\Components\Datagrids\NewDatagrid\Types\TextAlign;
use App\UI\Shared\Components\Datagrids\NewDatagrid\Actions;
use App\UI\Shared\Components\Datagrids\NewDatagrid\Columns;
use Latte\Essential\CachingIterator;
use Nette\Application\UI\Control;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 */
class DatagridControl extends Control
{
    /**
     * @var list<Columns\BaseColumn<T>>
     */
    public array $columns = [];

    /**
     * @var array<string, Columns\BaseColumn<T>>
     */
    private array $columnsMap = [];

    /**
     * @var list<Actions\BaseAction>
     */
    public array $actions = [];

    /**
     * @var array<string, Actions\BaseAction>
     */
    private array $actionsMap = [];

    /**
     * @var (\Closure(T, CachingIterator): string|null)|null
     */
    public protected(set) ?\Closure $rowCssCallback = null;

    public protected(set) string $dateFormat = 'Y-m-d H:i:s';

    public protected(set) string $emptyValue = 'N/A';

    /**
     * @param EntityList<T> $entityList
     */
    public function __construct(
        protected readonly EntityList $entityList,
        public ?string $noDataMsg = null
    ) {}

    public function render(): void
    {
        $this->template->entityList = $this->entityList;
        $this->template->render(__DIR__ . '/datagrid.latte');
    }

    public function getColumn(string $name): ?Columns\BaseColumn
    {
        return $this->columnsMap[$name] ?? null;
    }

    public function getAction(string $name): ?Actions\BaseAction
    {
        return $this->actionsMap[$name] ?? null;
    }

    /**
     * @param T $entity
     * @param CachingIterator $iterator
     */
    public function getRowCss(BaseEntity $entity, CachingIterator $iterator)
    {
        if ($this->rowCssCallback !== null) {
            return $this->rowCssCallback($entity, $iterator);
        }

        return $iterator->isOdd() ? 'odd' : '';
    }

    #region Columns Management

    /**
     * @param (\Closure(T): Html|string)|null $callback
     * @return Columns\CallableColumn<T>
     */
    public function addCallableColumn(string $name, string|Html $title, \Closure $callback, ?string $field = null): Columns\CallableColumn
    {
        $this->columns[] = $column = new Columns\CallableColumn($this, $name, $title, $field);
        $this->columnsMap[$name] = $column;
        $column->setCallback($callback);

        return $column;
    }

    /**
     * @return Columns\TextColumn<T>
     */
    public function addTextColumn(string $name, string|Html $title, ?string $field = null): Columns\TextColumn
    {
        $this->columns[] = $column = new Columns\TextColumn($this, $name, $title, $field);
        $this->columnsMap[$name] = $column;

        return $column;
    }

    /**
     * @return Columns\BooleanColumn<T>
     */
    public function addBoolColumn(string $name, string|Html $title, ?string $field = null): Columns\BooleanColumn
    {
        $this->columns[] = $column = new Columns\BooleanColumn($this, $name, $title, $field);
        $this->columnsMap[$name] = $column;
        return $column;
    }

    /**
     * @return Columns\IntegerColumn<T>
     */
    public function addIntegerColumn(string $name, string|Html $title, ?string $field = null): Columns\IntegerColumn
    {
        $this->columns[] = $column = new Columns\IntegerColumn($this, $name, $title, $field);
        $this->columnsMap[$name] = $column;
        return $column;
    }

    /**
     * @return Columns\FloatColumn<T>
     */
    public function addFloatColumn(string $name, string|Html $title, ?string $field = null): Columns\FloatColumn
    {
        $this->columns[] = $column = new Columns\FloatColumn($this, $name, $title, $field);
        $this->columnsMap[$name] = $column;
        return $column;
    }

    /**
     * @return Columns\DatetimeColumn<T>
     */
    public function addDatetimeColumn(string $name, string|Html $title, ?string $field = null): Columns\DatetimeColumn
    {
        $this->columns[] = $column = new Columns\DatetimeColumn($this, $name, $title, $field);
        $this->columnsMap[$name] = $column;
        return $column;
    }

    /**
     * @return Columns\ColorColumn<T>
     */
    public function addColorColumn(string $name, string|Html $title, ?string $field = null): Columns\ColorColumn
    {
        $this->columns[] = $column = new Columns\ColorColumn($this, $name, $title, $field);
        $this->columnsMap[$name] = $column;
        $column
            ->setWidth(ColumnWidth::Width01)
            ->setTextAlign(TextAlign::Center);
        return $column;
    }

    #endregion

    #region Action Management

    /**
     * @return Actions\Signal<T>
     */
    public function addSignal(
        string $name,
        Html|string|null $title,
        string $icon,
        ?string $target = null,
        ActionSeverity $severity = ActionSeverity::Info,
    ): Actions\Signal {
        $this->actions[] = $action = new Actions\Signal($this, $name, $target);
        $action->setIcon($icon);
        $action->setTitle($title);
        $action->setSeverity($severity);

        $this->actionsMap[$name] = $action;
        return $action;
    }

    /**
     * @return Actions\SideEffect<T>
     */
    public function addSideEffect(
        string $name,
        Html|string|null $title,
        string $icon,
        ?string $target = null,
        ActionSeverity $severity = ActionSeverity::Info,
    ): Actions\SideEffect {
        $this->actions[] = $action = new Actions\SideEffect($this, $name, $target);
        $action->setIcon($icon);
        $action->setTitle($title);
        $action->setSeverity($severity);

        $this->actionsMap[$name] = $action;
        return $action;
    }

    #endregion

    #region Setters

    public function setDateFormat(string $dateFormat): static
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    public function setEmptyValue(string $emptyValue): static
    {
        $this->emptyValue = $emptyValue;
        return $this;
    }

    /**
     * @param (\Closure(T, CachingIterator): string|null)|null $rowCssCallback
     */
    public function setRowCssCallback(\Closure $rowCssCallback): static
    {
        $this->rowCssCallback = $rowCssCallback;
        return $this;
    }

    #endregion
}
