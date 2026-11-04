<?php declare(strict_types=1);

namespace App\UI\Admin\Components\Datagrids;

use App\Model\Customer\CustomerEntity;
use App\Model\Customer\CustomerFilter;
use App\Model\Customer\CustomerSortBy;
use App\Model\EntityList;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\ActionSeverity;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\ColumnWidth;
use App\UI\Shared\Components\Datagrids\Datagrid\Types\TextAlign;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControl;
use App\UI\Shared\Components\Datagrids\Datagrid\DatagridControlFactory;
use Nette\Utils\Html;

class CustomerDefaultDatagridFactory
{
    public function __construct(
        private readonly DatagridControlFactory $datagridFactory,
    ) {}

    /**
     * @param EntityList<CustomerEntity> $entityList
     * @param list<int> $perPageOptions
     * @param int $itemsPerPage
     * @return DatagridControl<CustomerEntity>
     */
    public function create(
        EntityList $entityList,
        CustomerFilter $filter,
        array $perPageOptions,
        int $itemsPerPage = 20,
    ): DatagridControl {
        $grid = $this->datagridFactory->create(
            'Seznam zákazníků',
            $entityList,
            'Nebyl nalezen žádný zákazník!'
        );

        $grid->addGridSignal('add', 'Přidat zákazníka', 'icon-plus-sign');
        $grid->setSortBy($filter->sortBy);
        $grid->setSortDirection($filter->sortDirection);
        $grid->setPerPageOptions($perPageOptions, $itemsPerPage);

        $grid
            ->addIntegerColumn('id', 'ID')
            ->setSortableKey(CustomerSortBy::Id);

        $grid
            ->addTextColumn('fullname', 'Jméno')
            ->setSortableKey(CustomerSortBy::Fullname)
            ->addLinkFormatter('edit');
        $grid
            ->addTextColumn('email', 'Email')
            ->setSortableKey(CustomerSortBy::Email)
            ->addEmailFormatter('email');

        $grid
            ->addTextColumn('identityNumber', 'Rodné číslo')
            ->setSortableKey(CustomerSortBy::IdentityNumber);
        $grid
            ->addTextColumn('phone', 'Telefon')
            ->setSortableKey(CustomerSortBy::Phone);
        $grid
            ->addCallableColumn(
                'address',
                'Adresa',
                fn(CustomerEntity $customer) =>
                    Html::el()
                        ->addText("{$customer->street}")
                        ->addHtml('<br>')
                        ->addText("{$customer->city} {$customer->zip}")
            );
        $grid
            ->addDatetimeColumn('created', 'Datum založení')
            ->setSortableKey(CustomerSortBy::Created);

        $grid
            ->addRowSignal('edit', 'Spravovat zákazníka', 'icon-edit');
        $grid
            ->addRowSideEffect('delete', 'Smazat zákazníka', 'icon-remove', severity: ActionSeverity::Danger)
            ->setConfirmMsg(fn(CustomerEntity $customer) => "Opravdu chcete smazat zákazníka s id #{$customer->id}?");

        return $grid;
    }
}
