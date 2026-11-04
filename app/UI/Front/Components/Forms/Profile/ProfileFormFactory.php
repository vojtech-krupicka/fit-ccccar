<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\Profile;

use App\UI\Shared\Components\Forms\BootstrapFormFactory;
use Nette\Application\UI;

class ProfileFormFactory
{
    public function __construct(
        private readonly BootstrapFormFactory $formFactory,
    ) {}

    public function create(): UI\Form
    {
        $form = $this->formFactory->create();

        $this->formFactory->addGroup($form, 'Základní informace');
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
            ->addText('idNumber', 'Číslo OP.:')
            ->setRequired('Musíte vyplnit Vaše číslo občanského průkazu.')
            ->addRule(UI\Form::Length, 'Číslo občanského průkazu musí mít přesně %d znaků.', 9);
        $form
            ->addText('phone', 'Telefon:')
            ->setRequired('Musíte vyplnit Vaše telefonní číslo.')
            ->addRule(UI\Form::Pattern, 'Telefonní číslo musí být ve formátu [+42X]123456789.', '(\+42\d)?\d{9}')
            ->setEmptyValue('+420');

        $form
            ->addText('street', 'Ulice:')
            ->setRequired('Musíte vyplnit Vaši ulici.')
            ->addRule(UI\Form::MaxLength, 'Jméno ulice nesmí být delší než %d znaků.', 64);
        $form
            ->addText('city', 'Město:')
            ->setRequired('Musíte vyplnit Vaše město.')
            ->addRule(UI\Form::MaxLength, 'Jméno města nesmí být delší než %d znaků.', 64);
        $form
            ->addText('zip', 'PSČ:')
            ->setRequired('Musíte vyplnit Vaše PSČ.')
            ->addRule(UI\Form::Length, 'PSČ musí mít přesně %d znaků.', 5);
        $form
            ->addSelect('country', 'Země:')
            ->setRequired('Musíte vyplnit Vaši zemi.')
            ->setPrompt('--- Vyberte zemi původu ---')
            ->setItems([
                'cz' => 'Česká republika',
                'sk' => 'Slovensko',
                'pl' => 'Polsko',
                'de' => 'Německo',
                'at' => 'Rakousko',
            ]);

        $form->addSubmit('send', 'Uložit')->setOption('icon', 'icon-refresh');
        $form->addSubmit('cancel', 'Zrušit')->setOption('icon', 'icon-remove');

        return $form;
    }
}
