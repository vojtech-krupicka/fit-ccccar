<?php declare(strict_types=1);

namespace App\Model;

use Nette\Database\Table;

abstract class BaseEntity
{
    public int|null $id = null;
}
