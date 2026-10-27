<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\NewDatagrid\Columns;

use App\Model\BaseEntity;
use App\UI\Shared\Components\Datagrids\NewDatagrid\DatagridControl;
use App\UI\Shared\Components\Datagrids\NewDatagrid\Formatters;
use App\UI\Shared\Components\Datagrids\NewDatagrid\Types;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 */
abstract class BaseColumn
{
    use Types\GetEntityFieldTrait;

    /** @var list<Formatters\BaseFormatter> */
    public protected(set) array $formatters = [];
    public protected(set) Types\TextAlign $textAlign = Types\TextAlign::Center;
    public protected(set) Types\ColumnWidth $width = Types\ColumnWidth::NoWidth;
    public protected(set) bool $sortable = false;
    public protected(set) ?string $emptyValue = null;

    /**
     * @param DatagridControl<T> $datagrid
     * @param string $name
     * @param Html|string|null $title
     */
    public function __construct(
        public readonly DatagridControl $datagrid,
        public readonly string $name,
        public protected(set) Html|string|null $title = null,
        \Closure|string|null $field = null
    ) {
        $this->field = $field !== null ? $field : $this->name;
    }

    abstract public function getCellValue(BaseEntity $entity): Html|string|int|float|null;

    #region Formatters

    /**
     * @param (\Closure(T): string|null)|string $field
     */
    public function addColorFormatter(\Closure|string $field): static
    {
        $this->formatters[] = new Formatters\ColorFormatter($this, $field);
        return $this;
    }

    public function addLinkFormatter(string $target, \Closure|string|null $args = null): static
    {
        $this->formatters[] = new Formatters\LinkFormatter($this, $target, $args);
        return $this;
    }

    /**
     * @param (\Closure(T): string|null)|string $field
     */
    public function addUrlFormatter(\Closure|string $field): static
    {
        $this->formatters[] = new Formatters\UrlFormatter($this, $field);
        return $this;
    }

    /**
     * @param (\Closure(T): string|null)|string $field
     */
    public function addEmailFormatter(\Closure|string $field): static
    {
        $this->formatters[] = new Formatters\EmailFormatter($this, $field);
        return $this;
    }

    #endregion

    #region Getters

    /**
     * @param T $entity
     */
    public function getCellContent(BaseEntity $entity): Html|string
    {
        $value = $this->getCellValue($entity);
        if ($value === null || $value === '') {
            $value = $this->getEmptyValue();
        }

        // apply formatters (email/ulr/link/color)
        foreach ($this->formatters as $formatter) {
            $value = $formatter->format($entity, $value);
        }

        return $value;
    }

    public function getHeaderCss(): string
    {
        $css = [$this->textAlign->value];
        if ($this->width !== Types\ColumnWidth::NoWidth) {
            $css[] = $this->width->value;
        }
        return implode(' ', $css);
    }

    public function getCellCss(): string
    {
        return $this->textAlign->value;
    }

    public function getEmptyValue(): string
    {
        return $this->emptyValue ?? $this->datagrid->emptyValue;
    }

    #endregion

    #region Setters

    public function setTitle(Html|string|null $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function setSortable(bool $sortable): static
    {
        $this->sortable = $sortable;
        return $this;
    }

    public function setWidth(Types\ColumnWidth $width): static
    {
        $this->width = $width;
        return $this;
    }

    public function setTextAlign(Types\TextAlign $textAlign): static
    {
        $this->textAlign = $textAlign;
        return $this;
    }

    public function setEmptyValue(?string $emptyValue): static
    {
        $this->emptyValue = $emptyValue;
        return $this;
    }

    #endregion
}
