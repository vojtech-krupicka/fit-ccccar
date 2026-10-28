<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid;

use App\Model\BaseEntity;
use App\Model\EntityList;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\ActionSeverity;
use App\UI\Shared\Components\Datagrids\Datagrid\Actions;
use App\UI\Shared\Components\Datagrids\Datagrid\Columns;
use Latte\Essential\CachingIterator;
use Nette\Application\UI\Control;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 */
class DatagridControl extends Control
{
    /**
     * @var array<string, Columns\BaseColumn<T>>
     */
    public array $columns = [];

    /**
     * @var array<string, Actions\BaseAction<T>>
     */
    public array $actions = [];

    /**
     * @var (\Closure(T, CachingIterator): string)|null
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

    /**
     * @return Columns\BaseColumn<T>|null
     */
    public function getColumn(string $name): ?Columns\BaseColumn
    {
        return $this->columns[$name] ?? null;
    }

    /**
     * @return Actions\BaseAction<T>|null
     */
    public function getAction(string $name): ?Actions\BaseAction
    {
        return $this->actions[$name] ?? null;
    }

    /**
     * @param T $entity
     * @param CachingIterator $iterator
     */
    public function getRowCss(BaseEntity $entity, CachingIterator $iterator): string
    {
        if ($this->rowCssCallback !== null) {
            return ($this->rowCssCallback)($entity, $iterator);
        }

        return '';
    }

    #region Columns Management

    /**
     * @template C of Columns\BaseColumn<T>
     * @param C $column
     * @return C
     */
    protected function registerColumn(Columns\BaseColumn $column): Columns\BaseColumn
    {
        if (isset($this->columns[$column->name])) {
            throw new \InvalidArgumentException("Column '{$column->name}' already exists.");
        }
        return $this->columns[$column->name] = $column;
    }

    /**
     * @param (\Closure(T): (Html|string)) $callback
     * @return Columns\CallableColumn<T>
     */
    public function addCallableColumn(string $name, string|Html $title, \Closure $callback, ?string $field = null): Columns\CallableColumn
    {
        $column = new Columns\CallableColumn($this, $name, $title, $field);
        $column->setCallback($callback);

        $this->registerColumn($column);
        return $column;
    }

    /**
     * @return Columns\TextColumn<T>
     */
    public function addTextColumn(string $name, string|Html $title, ?string $field = null): Columns\TextColumn
    {
        $column = new Columns\TextColumn($this, $name, $title, $field);
        $this->registerColumn($column);
        return $column;
    }

    /**
     * @return Columns\BooleanColumn<T>
     */
    public function addBoolColumn(string $name, string|Html $title, ?string $field = null): Columns\BooleanColumn
    {
        $column = new Columns\BooleanColumn($this, $name, $title, $field);
        $this->registerColumn($column);
        return $column;
    }

    /**
     * @return Columns\IntegerColumn<T>
     */
    public function addIntegerColumn(string $name, string|Html $title, ?string $field = null): Columns\IntegerColumn
    {
        $column = new Columns\IntegerColumn($this, $name, $title, $field);
        $this->registerColumn($column);
        return $column;
    }

    /**
     * @return Columns\FloatColumn<T>
     */
    public function addFloatColumn(string $name, string|Html $title, ?string $field = null): Columns\FloatColumn
    {
        $column = new Columns\FloatColumn($this, $name, $title, $field);
        $this->registerColumn($column);
        return $column;
    }

    /**
     * @return Columns\DatetimeColumn<T>
     */
    public function addDatetimeColumn(string $name, string|Html $title, ?string $field = null): Columns\DatetimeColumn
    {
        $column = new Columns\DatetimeColumn($this, $name, $title, $field);
        $this->registerColumn($column);
        return $column;
    }

    #endregion

    #region Action Management

    /**
     * @template A of Actions\BaseAction<T>
     * @param A $action
     * @return A
     */
    protected function registerAction(Actions\BaseAction $action): Actions\BaseAction
    {
        if (isset($this->actions[$action->name])) {
            throw new \InvalidArgumentException("Action '{$action->name}' already exists.");
        }

        return $this->actions[$action->name] = $action;
    }

    /**
     * @return Actions\Signal<T>
     */
    public function addSignal(
        string $name,
        string|null $title,
        string $icon,
        ?string $target = null,
        ActionSeverity $severity = ActionSeverity::Info,
    ): Actions\Signal {
        $action = new Actions\Signal($this, $name, $target);
        $this->registerAction($action);

        $action->setIcon($icon);
        $action->setTitle($title);
        $action->setSeverity($severity);
        return $action;
    }

    /**
     * @return Actions\SideEffect<T>
     */
    public function addSideEffect(
        string $name,
        string|null $title,
        string $icon,
        ?string $target = null,
        ActionSeverity $severity = ActionSeverity::Info,
    ): Actions\SideEffect {
        $action = new Actions\SideEffect($this, $name, $target);
        $this->registerAction($action);

        $action->setIcon($icon);
        $action->setTitle($title);
        $action->setSeverity($severity);
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
     * @param (\Closure(T, CachingIterator): string)|null $rowCssCallback
     */
    public function setRowCssCallback(?\Closure $rowCssCallback): static
    {
        $this->rowCssCallback = $rowCssCallback;
        return $this;
    }

    #endregion
}
