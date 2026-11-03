<?php declare(strict_types=1);

namespace App\UI\Admin\RentalOffice;

use App\Model\RentalOffice\RentalOfficeEntity;
use App\Model\RentalOffice\RentalOfficeFacade;
use App\Model\RentalOffice\RentalOfficeFilter;
use App\Model\RentalOffice\RentalOfficeSortBy;
use App\Model\EntityList;
use App\Model\PageRequest;
use App\Model\SortDirection;
use App\UI\Admin\Components\Datagrids\RentalOfficeDefaultDatagridFactory;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControl;
use App\UI\Admin;

final class RentalOfficePresenter extends Admin\BaseSecuredPresenter
{
    /** @var EntityList<RentalOfficeEntity> */
    private EntityList $rentalOfficeList;
    private RentalOfficeFilter $filter;
    private int $itemsPerPage;

    private const PER_PAGE_OPTIONS = [5, 10, 20, 50];
    private const DEFAULT_ITEMS_PER_PAGE = 10;

    public function __construct(
        private readonly RentalOfficeFacade $rentalOfficeFacade,
        private readonly RentalOfficeDefaultDatagridFactory $defaultDatagridFactory
    ) {}

    public function actionDefault(
        int $page = 1,
        int $itemsPerPage = self::DEFAULT_ITEMS_PER_PAGE,
        ?string $sortBy = null,
        ?string $sortDirection = null,
    ): void {
        $this->itemsPerPage = in_array($itemsPerPage, self::PER_PAGE_OPTIONS, true) ? $itemsPerPage : self::DEFAULT_ITEMS_PER_PAGE;

        $this->filter = new RentalOfficeFilter(
            sortBy: RentalOfficeSortBy::tryFrom((string) $sortBy) ?? RentalOfficeSortBy::Id,
            sortDirection: SortDirection::tryFrom((string) $sortDirection) ?? SortDirection::Ascending,
        );

        $pageRequest = new PageRequest(page: $page, itemsPerPage: $this->itemsPerPage);
        $this->rentalOfficeList = $this->rentalOfficeFacade->find($this->filter, $pageRequest);
    }

    public function renderDefault(): void
    {
        $this->template->pageHeading = 'Seznam poboček';
        $this->addBreadcrumb('RentalOffice:default', 'Seznam poboček');
    }

    /**
     * @return DatagridControl<RentalOfficeEntity>
     */
    public function createComponentDefaultDatagrid(): DatagridControl
    {
        $grid = $this->defaultDatagridFactory->create(
            $this->rentalOfficeList,
            $this->filter,
            self::PER_PAGE_OPTIONS,
            $this->itemsPerPage,
        );
        return $grid;
    }
}
