<?php declare(strict_types=1);

namespace App\UI\Admin\CarRental;

use App\Model\CarRental\CarRentalEntity;
use App\Model\CarRental\CarRentalFacade;
use App\Model\CarRental\CarRentalFilter;
use App\Model\CarRental\CarRentalSortBy;
use App\Model\EntityList;
use App\Model\PageRequest;
use App\Model\SortDirection;
use App\UI\Admin\Components\Datagrids\CarRentalDefaultDatagridFactory;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControl;
use App\UI\Admin;

final class CarRentalPresenter extends Admin\BaseSecuredPresenter
{
    /** @var EntityList<CarRentalEntity> */
    private EntityList $carRentalList;
    private CarRentalFilter $filter;
    private int $itemsPerPage;

    private const PER_PAGE_OPTIONS = [5, 10, 20, 50];
    private const DEFAULT_ITEMS_PER_PAGE = 10;

    public function __construct(
        private readonly CarRentalFacade $rentalOfficeFacade,
        private readonly CarRentalDefaultDatagridFactory $defaultDatagridFactory
    ) {}

    public function actionDefault(
        int $page = 1,
        int $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE,
        ?string $sortBy = null,
        ?string $sortDirection = null,
    ): void {
        $this->itemsPerPage = in_array($itemsPerPage, self::PER_PAGE_OPTIONS, true) ? $itemsPerPage : self::DEFAULT_ITEMS_PER_PAGE;

        $this->filter = new CarRentalFilter(
            sortBy: CarRentalSortBy::tryFrom((string) $sortBy) ?? CarRentalSortBy::Id,
            sortDirection: SortDirection::tryFrom((string) $sortDirection) ?? SortDirection::Ascending,
        );

        $pageRequest = new PageRequest(page: $page, itemsPerPage: $this->itemsPerPage);
        $this->carRentalList = $this->rentalOfficeFacade->find($this->filter, $pageRequest);
    }

    public function renderDefault(): void
    {
        $this->template->pageHeading = 'Seznam výpůjček';
        $this->addBreadcrumb('CarRental:default', 'Seznam výpůjček');
    }

    /**
     * @return DatagridControl<CarRentalEntity>
     */
    public function createComponentDefaultDatagrid(): DatagridControl
    {
        $grid = $this->defaultDatagridFactory->create(
            $this->carRentalList,
            $this->filter,
            self::PER_PAGE_OPTIONS,
            $this->itemsPerPage,
        );
        return $grid;
    }
}
