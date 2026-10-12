<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\SignUp;

use App\UI\Front\Components\Forms\Controls\StrongPassword;
use App\UI\Shared\Components\Forms\BootstrapFormFactory;
use Nette\Application\LinkGenerator;
use Nette\Application\UI;
use Nette\Utils\Html;

class SignUpFormFactory
{
    public function __construct(
        private readonly BootstrapFormFactory $formFactory,
        private readonly LinkGenerator $linkGenerator,
    ) {}

    public function create(): UI\Form
    {
        $form = $this->formFactory->create();
        $this->formFactory->addGroup($form, 'Přihlašovací údaje');

        $form
            ->addEmail('email', 'Email:')
            ->setRequired('Musíte vyplnit Váš email.');

        $password = StrongPassword::add($form, 'password', 'Heslo:');
        $password->setRequired('Vyplňte prosím Vaše heslo.');

        $form
            ->addPassword('passwordConfirm', 'Heslo (pro kontrolu):')
            ->addRule(UI\Form::Equal, 'Hesla se neshodují!', $password)
            ->setRequired('Vyplňte prosím i heslo pro kontrolu.')
            ->setOmitted(true);

        $this->formFactory->addGroup($form, 'Základní informace');
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

        $this->formFactory->addGroup($form, 'Kontaktní informace');
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

        $this->formFactory->addGroup($form, 'Registrace');
        $url = $this->linkGenerator->link(':Front:Home:conditions');
        $label = Html::el()
            ->addText('Souhlasím s ')
            ->addHtml(Html::el('a', ['target' => '_blank'])->href($url)->setText('obchodními podmínkami'))
            ->addText('.');
        $form
            ->addCheckbox('aggred', $label)
            ->setRequired('Musíte souhlasit s obchodními podmínkami.')
            ->setOmitted(true);

        $form->addSubmit('send', 'Registrovat')->setOption('icon', 'icon-signin');
        $form->onSuccess[] = $this->onSuccess(...);

        return $form;
    }

    public function onSuccess(UI\Form $form, SignUpFormData $values): void
    {
        echo '<pre>';
        var_dump($values);
        echo '</pre>';
        die;
    }
}
