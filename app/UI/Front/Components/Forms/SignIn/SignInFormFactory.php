<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\SignIn;

use App\Core\Authentication\FrontAuthenticator;
use App\UI\Shared\Components\Forms\BootstrapFormFactory;
use Nette\Application\UI;
use Nette\Security;

class SignInFormFactory
{
    public function __construct(
        private readonly Security\User $user,
        private readonly BootstrapFormFactory $formFactory,
        private readonly FrontAuthenticator $authenticator,
    ) {}

    public function create(): UI\Form
    {
        $form = $this->formFactory->create();
        $this->formFactory->addGroup($form, 'Přihlášení');

        $form
            ->addText('username', '*Email:')
            ->setRequired('Musíte vyplnit Váš email do administračního systému.');

        $form
            ->addPassword('password', '*Heslo:')
            ->setRequired('Vyplňte prosím Vaše heslo.');

        $form->addCheckbox('persistent', 'Zůstat přihlášený?');

        $form->addSubmit('send', 'Přihlásit')->setOption('icon', 'icon-signin');
        $form->onSuccess[] = $this->onSuccess(...);

        return $form;
    }

    public function onSuccess(UI\Form $form, SignInFormData $values): void
    {
        try {
            if ($values->persistent) {
                $this->user->setExpiration('+7days', false);
            } else {
                $this->user->setExpiration('+30minutes', true);
            }

            $this->user->setAuthenticator($this->authenticator);
            $this->user->login($values->username, $values->password);
        } catch (Security\AuthenticationException $e) {
            $form->addError('Nesprávné přihlašovací jméno nebo heslo!');
        }
    }
}
