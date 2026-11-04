<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Menu\Navigation;

use Nette\Application\UI\Presenter;

class NavigationItem
{
    /**
     * Indicates whether the navigation item is currently active.
     */
    public bool $isActive = false;

    /**
     * Navigation item for the menu.
     *
     * @param Presenter $presenter The current presenter instance.
     * @param string $target
     * @param string $title
     * @param string|null $icon
     * @param array<string, mixed> $args
     */
    public function __construct(
        Presenter $presenter,
        public readonly string $target,
        public readonly string $title,
        public readonly string|null $icon,
        public readonly array $args = [],
        public readonly NavigationItemType $type = NavigationItemType::Link,
    ) {
        try {
            $this->isActive = $presenter->isLinkCurrent($target);
        } catch (\Throwable $e) {
            $this->isActive = false;
        }
    }

    public function isLink(): bool
    {
        return $this->type === NavigationItemType::Link;
    }

    public function isButton(): bool
    {
        return $this->type === NavigationItemType::Button;
    }
}
