<?php declare(strict_types=1);

namespace App\UI;

use App\UI\Shared\Components\Menu\Breadcrumb;
use App\UI\Shared\Components\Menu\Navigation;
use App\Model;
use Nette\DI\Attributes\Inject;
use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    #[Inject]
    public Model\Settings\AppSettings $appSettings;

    #[Inject]
    public Navigation\NavigationControlFactory $navigationControlFactory;

    #[Inject]
    public Breadcrumb\BreadcrumbControlFactory $breadcrumbControlFactory;

    protected function startup(): void
    {
        parent::startup();

        $this->absoluteUrls = true;
        $this->invalidLinkMode = self::InvalidLinkTextual;
    }

    public function beforeRender()
    {
        parent::beforeRender();

        $this->template->pageHeading = null;
        $this->template->breadcrumbItems = [];

        $this->template->args = $this->appSettings;
    }

    #region Navigation

    protected function createComponentNavigation(): Navigation\NavigationControl
    {
        return $this->navigationControlFactory->create();
    }

    #endregion

    #region Breadcrumb

    /**
     * @param string $target
     * @param string $title
     * @param array<string, mixed> $args
     */
    protected function addBreadcrumb(string $target, string $title, array $args = []): self
    {
        $this->template->breadcrumbItems[] = new Breadcrumb\BreadcrumbItem($target, $title, $args);
        return $this;
    }

    protected function createComponentBreadcrumb(): Breadcrumb\BreadcrumbControl
    {
        return $this->breadcrumbControlFactory->create();
    }

    #endregion

    #region Helpers

    public function flashMessage(string|\stdClass|\Stringable $message, string $type = 'info'): \stdClass
    {
        $flash = parent::flashMessage($message, $type);

        if ($this->isAjax()) {
            $this->redrawControl('flashes');
        }

        return $flash;
    }

    #endregion
}
