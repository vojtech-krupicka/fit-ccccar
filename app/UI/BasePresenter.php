<?php declare(strict_types=1);

namespace App\UI;

use App\Model;
use Nette\DI\Attributes\Inject;
use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    #[Inject]
    public Model\Settings\AppSettings $appSettings;

    public function startup(): void
    {
        parent::startup();

        $this->absoluteUrls = true;
        $this->invalidLinkMode = self::InvalidLinkWarning;
    }

    public function beforeRender()
    {
        parent::beforeRender();

        $this->template->pageHeading = null;
        $this->template->breadcrumbItems = [];

        $this->template->args = $this->appSettings;
    }

    public function flashMessage(string|\stdClass|\Stringable $message, string $type = 'info'): \stdClass
    {
        $flash = parent::flashMessage($message, $type);

        if ($this->isAjax()) {
            $this->redrawControl('flashes');
        }

        return $flash;
    }
}
