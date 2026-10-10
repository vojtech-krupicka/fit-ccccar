<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Menu\Navigation;

use Nette\Application\UI\Presenter;

final class NavigationItem
{
    public bool $isActive = false;

    public function __construct(
        Presenter $presenter,
        public readonly string $target,
        public readonly string $title,
        public readonly string|null $icon,
        public readonly array $args = [],
    ) {
        $this->isActive = $presenter->isLinkCurrent($target);
    }
}
