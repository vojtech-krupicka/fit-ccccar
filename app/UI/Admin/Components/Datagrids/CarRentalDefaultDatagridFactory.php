<?php declare(strict_types=1);

namespace App\UI\Admin\Components\Datagrids;

use App\Model\CarRental\CarRentalEntity;
use App\Model\CarRental\CarRentalFilter;
use App\Model\CarRental\CarRentalSortBy;
use App\Model\EntityList;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\ActionSeverity;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\ColumnWidth;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\TextAlign;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControl;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControlFactory;
use Nette\Utils\Html;

class CarRentalDefaultDatagridFactory
{
    public function __construct(
        private readonly DatagridControlFactory $datagridFactory,
    ) {}

    /**
     * @param EntityList<CarRentalEntity> $entityList
     * @param list<int> $perPageOptions
     * @param int $itemsPerPage
     * @return DatagridControl<CarRentalEntity>
     */
    public function create(
        EntityList $entityList,
        CarRentalFilter $filter,
        array $perPageOptions,
        int $itemsPerPage = 20,
    ): DatagridControl {
        $grid = $this->datagridFactory->create(
            'Seznam výpůjček',
            $entityList,
            'Nebyla nalezena žádná výpůjčka!'
        );

        $grid->addGridSignal('add', 'Přidat výpůjčku', 'icon-plus-sign');
        $grid->setSortBy($filter->sortBy);
        $grid->setSortDirection($filter->sortDirection);
        $grid->setPerPageOptions($perPageOptions, $itemsPerPage);

        $grid
            ->addIntegerColumn('id', 'ID')
            ->setSortableKey(CarRentalSortBy::Id);

        $grid
            ->addCallableColumn('vehicle', 'Vozidlo', fn(CarRentalEntity $carRental) => $carRental->vehicle?->getFullName() ?? '')
            ->setSortableKey(CarRentalSortBy::Vehicle)
            ->addLinkFormatter('Vehicle:edit')
            ->setWidth(ColumnWidth::Width02);

        $grid
            ->addTextColumn('customer.fullname', 'Zákazník')
            ->setSortableKey(CarRentalSortBy::Customer)
            ->addLinkFormatter('Customer:edit')
            ->setWidth(ColumnWidth::Width01);

        $grid
            ->addTextColumn('rentalOffice.name', 'Pobočka')
            ->setSortableKey(CarRentalSortBy::RentalOffice)
            ->addLinkFormatter('RentalOffice:edit')
            ->setWidth(ColumnWidth::Width01);

        $grid
            ->addTextColumn('borrower.fullname', 'Půjčující')
            ->setSortableKey(CarRentalSortBy::Borrower)
            ->addLinkFormatter('Employee:edit');

        $grid
            ->addTextColumn('recipient.fullname', 'Příjemce')
            ->setSortableKey(CarRentalSortBy::Recipient)
            ->addLinkFormatter('Employee:edit');

        $grid
            ->addFloatColumn('distance', 'Ujeto')
            ->setSortableKey(CarRentalSortBy::Distance);
        $grid
            ->addFloatColumn('price', 'Cena')
            ->setSortableKey(CarRentalSortBy::Price);
        $grid
            ->addDatetimeColumn('reservedAt', 'Vytvořeno')
            ->setSortableKey(CarRentalSortBy::ReservedAt);

        $grid
            ->addCallableColumn(
                'reserved',
                Html::el()->addText('Rezervováno od')->addHtml('<br>')->addText('Rezervováno do'),
                fn(CarRentalEntity $carRental) =>
                    Html::el()
                        ->addText($carRental->reservedFrom->format('Y-m-d H:i'))
                        ->addHtml('<br>')
                        ->addText($carRental->reservedTo->format('Y-m-d H:i'))
            )
            ->setSortableKey(CarRentalSortBy::ReservedFrom);

        $grid
            ->addCallableColumn(
                'startedAt',
                Html::el()->addText('Vypůjčeno')->addHtml('<br>')->addText('Vráceno'),
                fn(CarRentalEntity $carRental) =>
                    Html::el()
                        ->addText($carRental->startAt?->format('Y-m-d H:i') ?? '-')
                        ->addHtml('<br>')
                        ->addText($carRental->endAt?->format('Y-m-d H:i') ?? '-')
            )
            ->setSortableKey(CarRentalSortBy::StartAt);

        $grid
            ->addBoolColumn('deleted', 'Zrušeno')
            ->setTrueValue(Html::el('span', ['title' => 'Ano', 'class' => 'badge badge-success'])->setText('Ano'))
            ->setFalseValue(Html::el('span', ['title' => 'Ne', 'class' => 'badge badge-danger'])->setText('Ne'))
            ->setSortableKey(CarRentalSortBy::Deleted);

        $grid
            ->addRowSignal('edit', 'Spravovat výpůjčku', 'icon-edit');
        $grid
            ->addRowSideEffect('delete', 'Smazat výpůjčku', 'icon-remove', severity: ActionSeverity::Danger)
            ->setConfirmMsg(fn(CarRentalEntity $carRental) => "Opravdu chcete smazat výpůjčku s id #{$carRental->id}?");

        return $grid;
    }
}
