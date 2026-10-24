<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Columns;

use App\Model\BaseEntity;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControl;
use App\UI\Shared\Components\Datagrids\Datagrid\Utils;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 */
abstract class BaseColumn
{
    public protected(set) Utils\TextAlign $textAlign = Utils\TextAlign::Center;
    public protected(set) Utils\ColumnWidth $width = Utils\ColumnWidth::NoWidth;
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
        public protected(set) Html|string|null $title = null
    ) {}

    abstract protected function renderCellContent(BaseEntity $entity): Html|string|null;

    public function renderHeader(): string
    {
        $css = [];
        $css[] = "text-{$this->textAlign->value}";
        if ($this->width !== Utils\ColumnWidth::NoWidth) {
            $css[] = $this->width->value;
        }

        if ($this->title instanceof Html) {
            $content = $this->title->toHtml();
        } else if ($this->title !== null) {
            $content = (string) $this->title;
        } else {
            $content = '&nbsp;';
        }

        $class = join(' ', $css);
        return "<th class=\"{$class}\">{$content}</th>";
    }

    public function renderCell(BaseEntity $entity): string
    {
        $content = $this->renderCellContent($entity);

        if ($content instanceof Html) {
            $content = $content->toHtml();
        } else if ($content !== null) {
            $content = (string) $content;
        } else {
            $content = $this->getEmptyValue();
        }

        $css = [];
        $css[] = "text-{$this->textAlign->value}";

        $class = join(' ', $css);
        return "<td class=\"{$class}\">{$content}</td>";
    }

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

    public function setWidth(Utils\ColumnWidth $width): static
    {
        $this->width = $width;
        return $this;
    }

    public function setTextAlign(Utils\TextAlign $textAlign): static
    {
        $this->textAlign = $textAlign;
        return $this;
    }

    public function getEmptyValue(): string
    {
        return $this->emptyValue ?? $this->datagrid->emptyValue;
    }

    public function setEmptyValue(?string $emptyValue): static
    {
        $this->emptyValue = $emptyValue;
        return $this;
    }

    protected function getFieldValue(BaseEntity $entity): mixed
    {
        $parts = explode('.', $this->name);
        $current = $entity;
        foreach ($parts as $part) {
            if ($current === null) {
                return null;
            }
            $current = $current->{$part} ?? null;
        }

        return $current;
    }

    #endregion
}
