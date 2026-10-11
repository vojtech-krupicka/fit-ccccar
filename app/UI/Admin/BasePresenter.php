<?php declare(strict_types=1);

namespace App\UI\Admin;

use Nette\Bridges\SecurityHttp\SessionStorage;

abstract class BasePresenter extends \App\UI\BasePresenter
{
    public function checkRequirements($element): void
    {
        $storage = $this->getUser()->getStorage();
        if ($storage instanceof SessionStorage) {
            $storage->setNamespace('admin');
        }

        parent::checkRequirements($element);
    }
}
