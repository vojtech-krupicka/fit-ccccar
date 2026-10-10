<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Menu\Breadcrumb;

use Nette\Application\UI\Control;

final class BreadcrumbControl extends Control
{
    /**
     * @param list<BreadcrumbItem>|null $items
     */
    public function render(?array $items = null): void
    {
        $this->template->items = $items;
        $this->template->render(__DIR__ . '/breadcrumb.latte');
    }
}
