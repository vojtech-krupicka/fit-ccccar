<?php declare(strict_types=1);

namespace App\UI\Admin\SignIn;

use App\UI\Admin\Components\Forms\SignIn\SignInFormFactory;
use App\UI\Admin;
use Nette\Application\UI;

final class SignInPresenter extends Admin\BasePresenter
{
    public function __construct(
        private readonly SignInFormFactory $signInFormFactory
    ) {}

    protected function startup(): void
    {
        parent::startup();
        if ($this->getUser()->isLoggedIn()) {
            $this->redirect('Home:default');
        }
    }

    public function renderDefault(): void
    {
        $this->template->pageHeading = 'Přihlášení do administračního systému';
        $this->addBreadcrumb('default', 'Přihlášení');
    }

    protected function createComponentSignInForm(): UI\Form
    {
        $form = $this->signInFormFactory->create();

        $form->onSuccess[] = function () {
            $this->flashMessage('Byl jste úspěšně přihlášen', 'info');
            $this->redirect('Home:default');
        };

        return $form;
    }
}
