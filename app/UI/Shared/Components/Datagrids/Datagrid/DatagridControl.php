<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid;

use App\Model\BaseEntity;
use App\Model\EntityList;
use App\Model\SortDirection;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\ActionSeverity;
use App\UI\Shared\Components\Datagrids\Datagrid\Actions;
use App\UI\Shared\Components\Datagrids\Datagrid\Columns;
use App\UI\Shared\Components\Menu\Pagination\PaginationControl;
use App\UI\Shared\Components\Menu\Pagination\PaginationControlFactory;
use Latte\Essential\CachingIterator;
use Nette\Application\UI;
use Nette\Utils\Html;

/**
 * @template T of BaseEntity
 */
class DatagridControl extends UI\Control
{
    /**
     * @var array<string, Columns\BaseColumn<T>>
     */
    public array $columns = [];

    /**
     * @var array<string, Actions\BaseAction<T>>
     */
    public array $rowActions = [];

    /**
     * @var array<string, Actions\BaseAction<T>>
     */
    public array $gridActions = [];

    /**
     * @var array<string, Actions\BulkAction>
     */
    public array $bulkActions = [];

    /**
     * @var (\Closure(T, CachingIterator): string)|null
     */
    public protected(set) ?\Closure $rowCssCallback = null;

    public protected(set) string $dateFormat = 'Y-m-d H:i:s';

    public protected(set) string $emptyValue = 'N/A';

    public protected(set) ?\BackedEnum $sortBy = null;

    public protected(set) SortDirection $sortDirection = SortDirection::Ascending;

    public protected(set) SortDirection $defaultSortDirection = SortDirection::Ascending;

    /**
     * @var list<int>
     */
    public protected(set) array $perPageOptions = [];

    public protected(set) ?int $itemsPerPage = null;

    /**
     * @param EntityList<T> $entityList
     */
    public function __construct(
        private readonly PaginationControlFactory $paginationFactory,
        public string $heading,
        protected readonly EntityList $entityList,
        public string $noDataMsg = 'No data available'
    ) {}

    public function render(): void
    {
        $this->template->entityList = $this->entityList;
        $this->template->render(__DIR__ . '/datagrid.latte');
    }

    #region Subcomponents

    public function createComponentPagination(): PaginationControl
    {
        return $this->paginationFactory->create(
            page: $this->entityList->currentPage ?? 1,
            pageCount: $this->entityList->pageCount ?? 1,
            totalCount: $this->entityList->totalCount
        );
    }

    public function createComponentPerPageForm(): UI\Form
    {
        $form = new UI\Form();
        $form
            ->addSelect('itemsPerPage', 'Na stránku:', array_combine($this->perPageOptions, $this->perPageOptions))
            ->setDefaultValue($this->itemsPerPage)
            ->setHtmlAttribute('onchange', 'this.form.submit()');

        $form
            ->addSubmit('apply', 'Zobrazit')
            ->setOption('icon', 'icon-ok');

        $form
            ->onSuccess[] = function (UI\Form $form, \stdClass $values): void {
                $this->getPresenter()->redirect('this', [
                    'page' => null,
                    'itemsPerPage' => (int) $values->itemsPerPage,
                ]);
            };

        return $form;
    }

    public function createComponentBulkActionForm(): UI\Form
    {
        $bulkActions = array_map(fn(Actions\BulkAction $action): string => $action->label, $this->bulkActions);
        $idsOnThisPage = array_map(fn(BaseEntity $entity): ?int => $entity->id, $this->entityList->entities);
        $confirmMessages = [];
        foreach ($this->bulkActions as $name => $action) {
            if ($action->confirmMsg !== null) {
                $confirmMessages[$name] = $action->confirmMsg;
            }
        }

        $form = new UI\Form();
        $form->setHtmlAttribute('onsubmit',
            "const o = this.elements['bulkAction'].selectedOptions[0];"
                . ' return !o || !o.dataset.confirm || confirm(o.dataset.confirm);');

        $form
            ->addCheckboxList('bulkActionIds', null, array_fill_keys($idsOnThisPage, ''))
            ->setRequired('Vyberte alespoň jednu položku.');
        $form
            ->addSelect('bulkAction', null, $bulkActions)
            ->setPrompt('--- Hromadná akce ---')
            ->setOptionAttribute('data-confirm:', $confirmMessages)
            ->setRequired('Vyberte hromadnou akci, kterou chcete provést.');

        $form->addSubmit('bulkActionSubmit', 'Provést');

        $form->onSuccess[] = function (UI\Form $form, \stdClass $values): void {
            $action = $this->getBulkAction($values->bulkAction);
            if ($action === null) {
                $this->getPresenter()->flashMessage("Neplatná hromadná akce '{$values->bulkAction}'!", 'danger');
            } else {
                $action->execute(array_values(array_map('intval', $values->bulkActionIds)));
            }

            $this->getPresenter()->redirect('this');
        };

        return $form;
    }

    #endregion

    #region Getters

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
    public function getRowAction(string $name): ?Actions\BaseAction
    {
        return $this->rowActions[$name] ?? null;
    }

    /**
     * @return Actions\BaseAction<T>|null
     */
    public function getGridAction(string $name): ?Actions\BaseAction
    {
        return $this->gridActions[$name] ?? null;
    }

    /**
     * @return Actions\BulkAction|null
     */
    public function getBulkAction(string $name): ?Actions\BulkAction
    {
        return $this->bulkActions[$name] ?? null;
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

    #endregion

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

    #region Row Actions

    /**
     * @template A of Actions\BaseAction<T>
     * @param A $action
     * @return A
     */
    protected function registerRowAction(Actions\BaseAction $action): Actions\BaseAction
    {
        if (isset($this->rowActions[$action->name])) {
            throw new \InvalidArgumentException("Row action with name '{$action->name}' already exists.");
        }

        return $this->rowActions[$action->name] = $action;
    }

    /**
     * @return Actions\Signal<T>
     */
    public function addRowSignal(
        string $name,
        string|null $title,
        string $icon,
        ?string $target = null,
        ActionSeverity $severity = ActionSeverity::Info,
    ): Actions\Signal {
        $action = new Actions\Signal($this, $name, $target);
        $this->registerRowAction($action);

        $action->setIcon($icon);
        $action->setTitle($title);
        $action->setSeverity($severity);
        return $action;
    }

    /**
     * @return Actions\SideEffect<T>
     */
    public function addRowSideEffect(
        string $name,
        string|null $title,
        string $icon,
        ?string $target = null,
        ActionSeverity $severity = ActionSeverity::Info,
    ): Actions\SideEffect {
        $action = new Actions\SideEffect($this, $name, $target);
        $this->registerRowAction($action);

        $action->setIcon($icon);
        $action->setTitle($title);
        $action->setSeverity($severity);
        return $action;
    }

    #endregion

    #region Grid Actions

    /**
     * @template A of Actions\BaseAction<T>
     * @param A $action
     * @return A
     */
    protected function registerGridAction(Actions\BaseAction $action): Actions\BaseAction
    {
        if (isset($this->gridActions[$action->name])) {
            throw new \InvalidArgumentException("Grid action with name '{$action->name}' already exists.");
        }

        return $this->gridActions[$action->name] = $action;
    }

    /**
     * @return Actions\Signal<T>
     */
    public function addGridSignal(
        string $name,
        string|null $title,
        string $icon,
        ?string $target = null,
        ActionSeverity $severity = ActionSeverity::Info,
    ): Actions\Signal {
        $action = new Actions\Signal($this, $name, $target);
        $this->registerGridAction($action);

        $action->setIcon($icon);
        $action->setTitle($title);
        $action->setSeverity($severity);
        return $action;
    }

    /**
     * @return Actions\SideEffect<T>
     */
    public function addGridSideEffect(
        string $name,
        string|null $title,
        string $icon,
        ?string $target = null,
        ActionSeverity $severity = ActionSeverity::Info,
    ): Actions\SideEffect {
        $action = new Actions\SideEffect($this, $name, $target);
        $this->registerGridAction($action);

        $action->setIcon($icon);
        $action->setTitle($title);
        $action->setSeverity($severity);
        return $action;
    }

    #endregion

    #region Bulk Actions

    /**
     * @param \Closure(list<int>): void $callback
     * @return Actions\BulkAction
     */
    public function addBulkAction(string $name, string $label, \Closure $callback, ?string $confirmMsg = null): Actions\BulkAction
    {
        if (isset($this->bulkActions[$name])) {
            throw new \InvalidArgumentException("Bulk action with name '{$name}' already exists.");
        }

        $action = new Actions\BulkAction($name, $label, $callback, $confirmMsg);
        return $this->bulkActions[$action->name] = $action;
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

    public function setSortBy(?\BackedEnum $sortBy): static
    {
        $this->sortBy = $sortBy;
        return $this;
    }

    public function setSortDirection(SortDirection $sortDirection): static
    {
        $this->sortDirection = $sortDirection;
        return $this;
    }

    /**
     * @param list<int> $options The available items per page options
     * @param int $current The currently selected items per page
     */
    public function setPerPageOptions(array $options, int $current): static
    {
        $this->perPageOptions = $options;
        $this->itemsPerPage = $current;
        return $this;
    }

    #endregion
}
