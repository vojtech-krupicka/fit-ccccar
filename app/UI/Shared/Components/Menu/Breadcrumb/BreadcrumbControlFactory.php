<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Menu\Breadcrumb;

interface BreadcrumbControlFactory
{
    public function create(): BreadcrumbControl;
}
