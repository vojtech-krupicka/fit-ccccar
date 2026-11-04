<?php declare(strict_types=1);

namespace App\Model\Customer;

enum CustomerSortBy: string
{
    case Id = 'id';
    case Fullname = 'fullname';
    case Email = 'email';
    case Phone = 'phone';
    case IdentityNumber = 'identityNumber';
    case Created = 'created';
}
