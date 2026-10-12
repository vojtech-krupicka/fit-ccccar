<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Menu\Navigation;

use Nette\Application\UI\Control;
use Nette\Security\User;

class NavigationControl extends Control
{
    public function __construct(
        private readonly User $user,
    ) {}

    #region Front RenderMethods

    public function renderFrontMain(): void
    {
        $this->template->items = $this->getFrontMainItems();
        $this->template->render(__DIR__ . '/navigation.latte');
    }

    public function renderFrontUser(): void
    {
        $this->template->items = $this->getFrontUserItems();
        $this->template->render(__DIR__ . '/navigation.latte');
    }

    /**
     * @return list<NavigationItem>
     */
    private function getFrontMainItems(): array
    {
        $presenter = $this->getPresenter();
        return [
            new NavigationItem($presenter, 'Home:default', 'Domů', 'icon-home'),
            // new NavigationItem($presenter, 'Catalog:default', 'Katalog vozidel', 'icon-truck'),
            new NavigationItem($presenter, 'Home:offices', 'Pobočky', 'icon-globe'),
            new NavigationItem($presenter, 'Home:conditions', 'Obchodní podmínky', 'icon-info-sign'),
            new NavigationItem($presenter, 'Home:faq', 'Užitečné informace', 'icon-question-sign'),
            new NavigationItem($presenter, 'Home:contact', 'Kontakty', 'icon-phone-sign'),
        ];
    }

    /**
     * @return list<NavigationItem>
     */
    private function getFrontUserItems(): array
    {
        $presenter = $this->getPresenter();
        return $this->user->isLoggedIn() ? [
            new NavigationItem($presenter, 'Customer:profile', 'Můj profil', 'icon-cog'),
            new NavigationItem($presenter, 'Customer:loans', 'Výpůjčky', 'icon-road'),
            new NavigationItem($presenter, 'SignOut:default', 'Odhlásit se', 'icon-signout', type: NavigationItemType::Button),
        ] : [
            new NavigationItem($presenter, 'SignIn:default', 'Přihlásit se', 'icon-signin'),
            new NavigationItem($presenter, 'SignUp:default', 'Registrace', 'icon-cogs'),
        ];
    }

    #endregion

    #region Admin Render Methods

    public function renderAdminMain(): void
    {
        $this->template->items = $this->getAdminMainItems();
        $this->template->render(__DIR__ . '/navigation.latte');
    }

    public function renderAdminUser(): void
    {
        $this->template->items = $this->getAdminUserItems();
        $this->template->render(__DIR__ . '/navigation.latte');
    }

    /**
     * @return list<NavigationItem>
     */
    private function getAdminMainItems(): array
    {
        $presenter = $this->getPresenter();
        return [
            new NavigationItem($presenter, 'Home:default', 'Domů', 'icon-home'),
            new NavigationItem($presenter, 'Loan:default', 'Správa výpůjček', 'icon-road'),
            new NavigationItem($presenter, 'Vehicle:default', 'Správa vozidel', 'icon-truck'),
            new NavigationItem($presenter, 'Customer:default', 'Správa zákazníků', 'icon-group')
        ];
    }

    /**
     * @return list<NavigationItem>
     */
    private function getAdminUserItems(): array
    {
        $presenter = $this->getPresenter();
        return $this->user->isLoggedIn() ? [
            new NavigationItem($presenter, 'Home:profile', 'Můj profil', 'icon-cog'),
            new NavigationItem($presenter, 'SignOut:default', 'Odhlásit se', 'icon-signout', type: NavigationItemType::Button),
        ] : [
            new NavigationItem($presenter, 'SignIn:default', 'Přihlásit se', 'icon-signin'),
        ];
    }

    #endregion
}
