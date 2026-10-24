<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid;

use App\Model\BaseEntity;
use App\Model\EntityList;
use App\UI\Shared\Components\Datagrids\Datagrid\Columns;
use App\UI\Shared\Components\Datagrids\Datagrid\Options;
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
     * @var list<Options\BaseOption>
     */
    protected array $options = [];

    /**
     * @var (\Closure(T): string)|null
     */
    public protected(set) ?\Closure $rowCallback = null;

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

    #region Columns Management

    /**
     * @param string $name
     * @param string|Html $title
     * @param (\Closure(T): string)|null $callback
     * @return Columns\TextColumn<T>
     */
    public function addTextColumn(string $name, string|Html $title, ?callable $callback = null): Columns\TextColumn
    {
        $column = new Columns\TextColumn($this, $name, $title);
        $column->setCallback($callback);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @return Columns\BooleanColumn<T>
     */
    public function addBoolColumn(string $name, string|Html $title): Columns\BooleanColumn
    {
        $this->columns[] = $column = new Columns\BooleanColumn($this, $name, $title);
        return $column;
    }

    /**
     * @return Columns\IntegerColumn<T>
     */
    public function addIntegerColumn(string $name, string|Html $title): Columns\IntegerColumn
    {
        $this->columns[] = $column = new Columns\IntegerColumn($this, $name, $title);
        return $column;
    }

    /**
     * @return Columns\FloatColumn<T>
     */
    public function addFloatColumn(string $name, string|Html $title): Columns\FloatColumn
    {
        $this->columns[] = $column = new Columns\FloatColumn($this, $name, $title);
        return $column;
    }

    /**
     * @return Columns\DatetimeColumn<T>
     */
    public function addDatetimeColumn(string $name, string|Html $title): Columns\DatetimeColumn
    {
        $this->columns[] = $column = new Columns\DatetimeColumn($this, $name, $title);
        return $column;
    }

    /**
     * @return Columns\EmailColumn<T>
     */
    public function addEmailColumn(string $name, string|Html $title): Columns\EmailColumn
    {
        $this->columns[] = $column = new Columns\EmailColumn($this, $name, $title);
        return $column;
    }

    /**
     * @param array<string, mixed> $args
     * @return Columns\LinkColumn<T>
     */
    public function addLinkColumn(string $name, string|Html $title, string $target, array $args = []): Columns\LinkColumn
    {
        $this->columns[] = $column = new Columns\LinkColumn($this, $name, $title);
        $column->setTarget($target);
        $column->setArgs($args);
        return $column;
    }

    /**
     * @return Columns\UrlColumn<T>
     */
    public function addUrlColumn(string $name, string|Html $title): Columns\UrlColumn
    {
        $this->columns[] = $column = new Columns\UrlColumn($this, $name, $title);
        return $column;
    }

    #endregion

    #region Setters

    /**
     * @param \Closure(T): string $callback
     */
    public function setRowCallback(\Closure $callback): static
    {
        $this->rowCallback = $callback;
        return $this;
    }

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

    #endregion
}
