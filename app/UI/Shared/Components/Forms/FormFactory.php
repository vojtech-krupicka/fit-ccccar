<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Forms;

use Nette\Application\UI;

class FormFactory
{
    public function create(): UI\Form
    {
        $form = new UI\Form();
        return $form;
    }
}
