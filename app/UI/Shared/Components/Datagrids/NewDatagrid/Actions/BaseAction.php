<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\NewDatagrid\Actions;

use App\Model\BaseEntity;
use App\UI\Shared\Components\Datagrids\NewDatagrid\Types\ActionSeverity;
use App\UI\Shared\Components\Datagrids\NewDatagrid\Types\GetEntityFieldTrait;
use App\UI\Shared\Components\Datagrids\NewDatagrid\Types\LinkArgsTrait;
use App\UI\Shared\Components\Datagrids\NewDatagrid\DatagridControl;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 */
abstract class BaseAction
{
    use GetEntityFieldTrait;
    use LinkArgsTrait;

    const DEFAULT_ACTION_CSS = 'btn btn-default btn-xs';

    /**
     * @var array<string, mixed>|(\Closure(T): array<string, mixed>)
     */
    public protected(set) array|\Closure|null $args = null;

    public protected(set) string|null $title = null;

    public protected(set) ?string $target = null;

    public protected(set) string|null $icon = null;

    public protected(set) ActionSeverity $severity = ActionSeverity::Primary;

    /**
     * @param DatagridControl<T> $datagrid
     * @param string $name
     * @param string|null $target
     */
    public function __construct(
        public readonly DatagridControl $datagrid,
        public readonly string $name,
        ?string $target = null,
    ) {
        $this->target = $target;
        if ($this->target === null) {
            $this->target = $this->name;
        }
    }

    /**
     * @param T $entity
     * @return Html
     */
    public abstract function render(BaseEntity $entity): Html;

    /**
     * @param T $entity
     * @return string
     */
    protected function getLink(BaseEntity $entity): string
    {
        $args = $this->buildArgs($entity);
        return $this->datagrid->getPresenter()->link($this->target, $args);
    }

    protected function getIcon(BaseEntity $entity): Html
    {
        return Html::el('span', [
            'class' => implode(' ', [$this->icon, $this->severity->value]),
        ]);
    }

    #region Setters

    /**
     * @param array<string, mixed>|(\Closure(T): array<string, mixed>)|null $args
     * @return static
     */
    public function setArgs(array|\Closure|null $args): static
    {
        $this->args = $args;
        return $this;
    }

    public function setTitle(string|null $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function setTarget(?string $target): static
    {
        $this->target = $target;
        return $this;
    }

    public function setIcon(string|null $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function setSeverity(ActionSeverity $severity): static
    {
        $this->severity = $severity;
        return $this;
    }

    #endregion
}
