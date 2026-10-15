<?php declare(strict_types=1);

namespace App\Model;

enum SortMode: string
{
    case Ascending = 'asc';
    case Descending = 'desc';
}
