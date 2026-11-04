<?php declare(strict_types=1);

namespace App\UI\Shared\Components\Forms;

use App\UI\Shared\Components\Forms\FormFactory;
use Nette\Application\UI;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\CheckboxList;
use Nette\Forms\Controls\RadioList;
use Nette\Forms\Rendering\DefaultFormRenderer;
use Nette\Utils\Html;

class BootstrapFormFactory
{
    public function __construct(
        private readonly FormFactory $formFactory
    ) {}

    private function makeBootstrap3(UI\Form $form): void
    {
        $form->getElementPrototype()->class('form-horizontal');
        $renderer = new DefaultFormRenderer();
        $renderer->wrappers['group']['container'] = 'div class="panel panel-default"';
        $renderer->wrappers['group']['label'] = 'div class="panel-heading"';
        $renderer->wrappers['controls']['container'] = 'div class="panel-body"';
        $renderer->wrappers['pair']['container'] = 'div class=form-group';
        $renderer->wrappers['pair']['.error'] = 'has-error';
        $renderer->wrappers['control']['container'] = 'div class=col-sm-9';
        $renderer->wrappers['label']['container'] = 'div class="col-sm-3 control-label"';
        $renderer->wrappers['control']['description'] = 'span class=help-block';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';
        $form->setRenderer($renderer);

        $usedPrimary = false;
        foreach ($form->getControls() as $control) {
            if (!$control instanceof BaseControl) {
                continue;
            }

            $type = $control->getOption('type');
            if ($type === 'button') {
                $control->getControlPrototype()->addClass($usedPrimary ? 'btn btn-default' : 'btn btn-primary');
                $usedPrimary = true;

                $icon = $control->getOption('icon');
                if (is_string($icon)) {
                    $control->setCaption(
                        Html::el()
                            ->addHtml(Html::el('span', ['class' => $icon]))
                            ->addText(' ' . $control->getCaption()),
                    );
                }
            } elseif (in_array($type, ['text', 'textarea', 'select'], true)) {
                $control->getControlPrototype()->addClass('form-control');
            } elseif (
                in_array($type, ['checkbox', 'radio'], true) &&
                ($control instanceof Checkbox || $control instanceof CheckboxList || $control instanceof RadioList)
            ) {
                $control->getContainerPrototype()->setName('div')->addClass($type);
            }
        }
    }

    public function getGroupTitle(string $title): Html
    {
        return Html::el('h3 class=panel-title')->setText($title);
    }

    public function addGroup(UI\Form $form, string $title): void
    {
        $form->addGroup(Html::el('h3 class=panel-title')->setText($title));
    }

    public function create(): UI\Form
    {
        $form = $this->formFactory->create();
        $form->onRender[] = $this->makeBootstrap3(...);

        return $form;
    }
}
