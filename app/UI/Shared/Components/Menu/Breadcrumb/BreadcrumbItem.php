<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Menu\Breadcrumb;

class BreadcrumbItem
{
    /**
     * @param string $target The target of the breadcrumb item
     * @param string $title The title of the breadcrumb item
     * @param array<string, mixed> $args Optional array of argument names and their values
     */
    public function __construct(
        public readonly string $target,
        public readonly string $title,
        public readonly array $args = [],  // array of argument names and their values
    ) {}
}
