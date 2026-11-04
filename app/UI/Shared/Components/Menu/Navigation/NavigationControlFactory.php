<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Menu\Navigation;

interface NavigationControlFactory
{
    public function create(): NavigationControl;
}
