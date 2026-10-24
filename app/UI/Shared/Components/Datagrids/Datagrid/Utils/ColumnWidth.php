<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Datagrids\Datagrid\Utils;

enum ColumnWidth: string
{
    case NoWidth = '';
    case Width01 = 'col-md-1';
    case Width02 = 'col-md-2';
    case Width03 = 'col-md-3';
    case Width04 = 'col-md-4';
    case Width05 = 'col-md-5';
    case Width06 = 'col-md-6';
    case Width07 = 'col-md-7';
    case Width08 = 'col-md-8';
    case Width09 = 'col-md-9';
    case Width10 = 'col-md-10';
    case Width11 = 'col-md-11';
    case Width12 = 'col-md-12';
}
