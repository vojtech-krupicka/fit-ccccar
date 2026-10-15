<?php declare(strict_types=1);

namespace App\Model;

enum SortDirection: string
{
    case Ascending = 'ASC';
    case Descending = 'DESC';
}
