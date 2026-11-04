<?php declare(strict_types=1);

namespace App\UI\Admin\Customer;

use App\Core\Attributes\PostSideEffect;
use App\Model\Customer\CustomerEntity;
use App\Model\Customer\CustomerFacade;
use App\Model\Customer\CustomerFilter;
use App\Model\Customer\CustomerSortBy;
use App\Model\EntityList;
use App\Model\PageRequest;
use App\Model\SortDirection;
use App\UI\Admin\Components\Datagrids\CustomerDefaultDatagridFactory;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControl;
use App\UI\Admin;

final class CustomerPresenter extends Admin\BaseSecuredPresenter
{
    /** @var EntityList<CustomerEntity> */
    private EntityList $customerList;
    private CustomerFilter $filter;
    private int $itemsPerPage;

    private const PER_PAGE_OPTIONS = [5, 10, 20, 50];
    private const DEFAULT_ITEMS_PER_PAGE = 10;

    public function __construct(
        private readonly CustomerFacade $customerFacade,
        private readonly CustomerDefaultDatagridFactory $defaultDatagridFactory
    ) {}

    #region Other actions

    #[PostSideEffect]
    public function actionDelete(int $id): void
    {
        $customer = $this->customerFacade->get($id);
        if ($customer === null) {
            $this->flashMessage("Zákazník s ID {$id} nebyl nalezen.", 'danger');
        } else {
            $this->flashMessage("Zákazník s ID {$id} byl smazán (FAKE).");
        }

        $this->redirect('default');
    }

    public function actionAdd(): void
    {
        $this->flashMessage("Funkcionalita 'přidat zákazníka' není implementována.", 'warning');
        $this->redirect('default');
    }

    public function actionEdit(int $id): void
    {
        $this->flashMessage("Funkcionalita 'upravit zákazníka #{$id}' není implementována.", 'warning');
        $this->redirect('default');
    }

    #endregion

    public function actionDefault(
        int $page = 1,
        int $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE,
        ?string $sortBy = null,
        ?string $sortDirection = null,
    ): void {
        $this->itemsPerPage = in_array($itemsPerPage, self::PER_PAGE_OPTIONS, true) ? $itemsPerPage : self::DEFAULT_ITEMS_PER_PAGE;

        $this->filter = new CustomerFilter(
            sortBy: CustomerSortBy::tryFrom((string) $sortBy) ?? CustomerSortBy::Id,
            sortDirection: SortDirection::tryFrom((string) $sortDirection) ?? SortDirection::Ascending,
        );

        $pageRequest = new PageRequest(page: $page, itemsPerPage: $this->itemsPerPage);
        $this->customerList = $this->customerFacade->find($this->filter, $pageRequest);
    }

    public function renderDefault(): void
    {
        $this->template->pageHeading = 'Seznam zákazníků';
        $this->addBreadcrumb('Customer:default', 'Seznam zákazníků');
    }

    /**
     * @return DatagridControl<CustomerEntity>
     */
    public function createComponentDefaultDatagrid(): DatagridControl
    {
        $grid = $this->defaultDatagridFactory->create(
            $this->customerList,
            $this->filter,
            self::PER_PAGE_OPTIONS,
            $this->itemsPerPage,
        );
        return $grid;
    }
}
