<?php declare(strict_types=1);

namespace App\Core\Attributes;

use Nette\Application\Attributes\Requires;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class PostSideEffect extends Requires
{
    public function __construct()
    {
        parent::__construct(methods: 'POST', sameOrigin: true);
    }
}
