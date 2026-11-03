<?php declare(strict_types=1);

namespace App\UI\Front\Components\Forms\Controls;

use Nette\Application\UI;
use Nette\Forms\Container;
use Nette\Forms\Controls;

class StrongPassword
{
    public static function add(
        Container $container,
        string $name,
        ?string $label = null,
    ): Controls\TextInput {
        // Validation will be handled by the rule added to the control below
        $control = $container->addPassword($name, $label);
        $control
            ->addRule(UI\Form::MinLength, 'Heslo musí být dlouhé alespoň %d znaků!', 10)
            ->addRule(UI\Form::Pattern, 'Heslo musí obsahovat alespoň jedno malé písmeno.', '.*[a-z].*')
            ->addRule(UI\Form::Pattern, 'Heslo musí obsahovat alespoň jedno velké písmeno.', '.*[A-Z].*')
            ->addRule(UI\Form::Pattern, 'Heslo musí obsahovat alespoň jedno číslo.', '.*\d.*')
            ->addRule(UI\Form::Pattern, 'Heslo musí obsahovat alespoň jeden speciální znak.', '.*[\W_].*');

        return $control;
    }
}
