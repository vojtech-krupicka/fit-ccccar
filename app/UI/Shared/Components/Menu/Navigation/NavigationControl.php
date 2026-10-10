<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Menu\Navigation;

use Nette\Application\UI\Control;
use Nette\Security\User;

final class NavigationControl extends Control
{
    public function __construct(
        private readonly User $user,
    ) {}

    public function renderFrontMain(): void
    {
        $this->template->items = $this->getFrontMainItems();
        $this->template->render(__DIR__ . '/front-main.latte');
    }

    public function renderFrontUser(): void
    {
        $this->template->items = $this->getFrontUserItems();
        $this->template->render(__DIR__ . '/front-user.latte');
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
            new NavigationItem($presenter, 'Home:profile', 'Můj profil', 'icon-dashboard'),
            new NavigationItem($presenter, 'Home:loans', 'Výpůjčky', 'icon-user'),
            new NavigationItem($presenter, 'Home:logout', 'Odhlášení', 'icon-cog'),
        ] : [
            new NavigationItem($presenter, 'Home:login', 'Přihlášení', 'icon-signin'),
            new NavigationItem($presenter, 'Home:register', 'Registrace', 'icon-user'),
        ];
    }
}
