<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\Profile;

use App\UI\Front\Components\Forms\Controls\StrongPassword;
use App\UI\Shared\Components\Forms\BootstrapFormFactory;
use Nette\Application\UI;

class ChangePasswordFormFactory
{
    public function __construct(
        private readonly BootstrapFormFactory $formFactory,
    ) {}

    public function create(): UI\Form
    {
        $form = $this->formFactory->create();

        $this->formFactory->addGroup($form, 'Změna hesla');

        $form
            ->addPassword('oldPassword', 'Staré heslo:')
            ->setRequired('Vyplňte prosím Vaše aktuální heslo.');

        $newPassword = StrongPassword::add($form, 'newPassword', 'Nové heslo:');
        $newPassword->setRequired('Vyplňte prosím Vaše nové heslo.');

        $form
            ->addPassword('newPasswordConfirm', 'Nové heslo (pro kontrolu):')
            ->addRule(UI\Form::Equal, 'Hesla se neshodují!', $newPassword)
            ->setRequired('Vyplňte prosím i heslo pro kontrolu.')
            ->setOmitted(true);

        $form->addSubmit('send', 'Změnit heslo')->setOption('icon', 'icon-refresh');
        $form->addSubmit('cancel', 'Zrušit')->setOption('icon', 'icon-remove');

        return $form;
    }
}
