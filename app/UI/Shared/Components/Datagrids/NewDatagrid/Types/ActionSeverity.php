<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\NewDatagrid\Types;

enum ActionSeverity: string
{
    case Info = 'text-info';
    case Primary = 'text-primary';
    case Warning = 'text-warning';
    case Success = 'text-success';
    case Danger = 'text-danger';
}
