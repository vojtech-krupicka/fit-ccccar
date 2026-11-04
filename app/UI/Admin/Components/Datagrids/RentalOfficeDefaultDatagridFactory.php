<?php declare(strict_types=1);

namespace App\UI\Admin\Components\Datagrids;

use App\Model\RentalOffice\RentalOfficeEntity;
use App\Model\RentalOffice\RentalOfficeFilter;
use App\Model\RentalOffice\RentalOfficeSortBy;
use App\Model\EntityList;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\ActionSeverity;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\ColumnWidth;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\TextAlign;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControl;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControlFactory;
use Nette\Utils\Html;

class RentalOfficeDefaultDatagridFactory
{
    public function __construct(
        private readonly DatagridControlFactory $datagridFactory,
    ) {}

    /**
     * @param EntityList<RentalOfficeEntity> $entityList
     * @param list<int> $perPageOptions
     * @param int $itemsPerPage
     * @return DatagridControl<RentalOfficeEntity>
     */
    public function create(
        EntityList $entityList,
        RentalOfficeFilter $filter,
        array $perPageOptions,
        int $itemsPerPage = 20,
    ): DatagridControl {
        $grid = $this->datagridFactory->create(
            'Seznam poboček',
            $entityList,
            'Nebyla nalezena žádná pobočka!'
        );

        $grid->addGridSignal('add', 'Přidat pobočku', 'icon-plus-sign');
        $grid->setSortBy($filter->sortBy);
        $grid->setSortDirection($filter->sortDirection);
        $grid->setPerPageOptions($perPageOptions, $itemsPerPage);

        $grid
            ->addIntegerColumn('id', 'ID')
            ->setSortableKey(RentalOfficeSortBy::Id);
        $grid
            ->addTextColumn('name', 'Název')
            ->setSortableKey(RentalOfficeSortBy::Name)
            ->addLinkFormatter('edit');
        $grid
            ->addTextColumn('supervisor.fullname', 'Vedoucí')
            ->addEmailFormatter('supervisor.email');
        $grid
            ->addCallableColumn('address', 'Adresa', fn(RentalOfficeEntity $rentalOffice) => Html::el()->addText("{$rentalOffice->street}")->addHtml('<br>')->addText("{$rentalOffice->city} {$rentalOffice->zip}"));
        $grid
            ->addTextColumn('email', 'Email')
            ->addEmailFormatter('email');
        $grid
            ->addTextColumn('phone', 'Telefon');

        $grid
            ->addRowSignal('edit', 'Spravovat pobočku', 'icon-edit');
        $grid
            ->addRowSideEffect('delete', 'Smazat pobočku', 'icon-remove', severity: ActionSeverity::Danger)
            ->setConfirmMsg(fn(RentalOfficeEntity $rentalOffice) => "Opravdu chcete smazat pobočku s id #{$rentalOffice->id}?");

        return $grid;
    }
}
