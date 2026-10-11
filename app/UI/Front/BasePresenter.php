<?php declare(strict_types=1);

namespace App\UI\Front;

use Nette\Bridges\SecurityHttp\SessionStorage;

abstract class BasePresenter extends \App\UI\BasePresenter
{
    public function checkRequirements($element): void
    {
        $storage = $this->getUser()->getStorage();
        if ($storage instanceof SessionStorage) {
            $storage->setNamespace('front');
        }

        parent::checkRequirements($element);
    }
}
