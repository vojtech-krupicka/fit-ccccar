<?php declare(strict_types=1);

namespace App\UI\Admin\Components\Forms\Profile;

use App\Model\RentalOffice\RentalOfficeFacade;
use App\UI\Shared\Components\Forms\BootstrapFormFactory;
use Nette\Application\UI;

class ProfileFormFactory
{
    public function __construct(
        private readonly BootstrapFormFactory $formFactory,
        private readonly RentalOfficeFacade $rentalOfficeFacade,
    ) {}

    public function create(): UI\Form
    {
        $form = $this->formFactory->create();

        $this->formFactory->addGroup($form, 'Základní informace');
        $form
            ->addSelect('rentalOffice', 'Pobočka:')
            ->setPrompt('--- Vyberte pobočku ---')
            ->setItems(
                array_column($this->rentalOfficeFacade->getRentalOfficeList(), 'name', 'id')
            );

        $form
            ->addEmail('email', 'Email:')
            ->setRequired('Musíte vyplnit Váš email.');
        $form
            ->addText('firstName', 'Jméno:')
            ->setRequired('Musíte vyplnit Vaše jméno.')
            ->addRule(UI\Form::MaxLength, 'Jméno nesmí být delší než %d znaků.', 64);
        $form
            ->addText('lastName', 'Příjmení:')
            ->setRequired('Musíte vyplnit Vaše příjmení.')
            ->addRule(UI\Form::MaxLength, 'Příjmení nesmí být delší než %d znaků.', 64);
        $form
            ->addText('phone', 'Telefon:')
            ->setRequired('Musíte vyplnit Vaše telefonní číslo.')
            ->addRule(UI\Form::Pattern, 'Telefonní číslo musí být ve formátu [+42X]123456789.', '(\+42\d)?\d{9}')
            ->setEmptyValue('+420');

        $form->addSubmit('send', 'Uložit')->setOption('icon', 'icon-refresh');
        $form->addSubmit('cancel', 'Zrušit')->setOption('icon', 'icon-remove');

        return $form;
    }
}
