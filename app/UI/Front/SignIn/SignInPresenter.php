<?php declare(strict_types=1);

namespace App\UI\Front\SignIn;

use App\UI\Front\Components\Forms\SignIn\SignInFormFactory;
use App\UI\Front;
use Nette\Application\UI;
use Nette\Forms\Rendering\DefaultFormRenderer;

final class SignInPresenter extends Front\BasePresenter
{
    public function __construct(
        private readonly SignInFormFactory $signInFormFactory
    ) {}

    public function actionDefault(): void
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect('Home:default');
        }
    }

    public function renderDefault(): void
    {
        $this->template->pageHeading = "Přihlášení do {$this->appSettings->info->company}";
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
