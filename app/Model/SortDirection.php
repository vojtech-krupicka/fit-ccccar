<?php declare(strict_types=1);

namespace App\Model;

enum SortDirection: string
{
    case Ascending = 'asc';
    case Descending = 'desc';

    public function opposite(): self
    {
        return $this === self::Ascending ? self::Descending : self::Ascending;
    }

    public function isAscending(): bool
    {
        return $this === self::Ascending;
    }

    public function isDescending(): bool
    {
        return $this === self::Descending;
    }
}
