<?php declare(strict_types=1);

namespace Tests;

use Nette\Application\IPresenterFactory;
use Nette\Application\Request;
use Nette\Application\Response;
use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;

/**
 * Base class for presenter tests: runs a presenter from the real DI container
 * (against the test database) and returns its response.
 *
 * Add helpers here as the application grows, for example loginAs() once you have authentication,
 * or an upload sandbox (a resources directory under temp/ set in config/local.test.neon).
 */
abstract class PresenterTestCase extends DatabaseTestCase
{
    /**
     * @param array<string, mixed> $params
     * @param array<string, mixed> $post
     * @param array<string, mixed> $files
     */
    protected function runPresenter(string $name, array $params = [], string $method = 'GET', array $post = [], array $files = []): Response
    {
        $presenter = self::container()->getByType(IPresenterFactory::class)->createPresenter($name);
        $this->assertInstanceOf(Presenter::class, $presenter);
        $presenter->autoCanonicalize = false;

        return $presenter->run(new Request($name, $method, $params, $post, $files));
    }

    /**
     * Submits a form component the way a browser does: POST with the "<form>-submit" signal.
     *
     * @param array<string, mixed> $post
     * @param array<string, mixed> $params
     * @param array<string, mixed> $files
     */
    protected function submitForm(string $presenter, string $form, array $post, array $params = [], array $files = []): Response
    {
        return $this->runPresenter($presenter, ['do' => "$form-submit"] + $params, 'POST', $post, $files);
    }

    /**
     * Fires a signal (handleXxx) the way a clicked link does: GET with the "do" parameter.
     *
     * @param array<string, mixed> $params signal arguments and action parameters
     */
    protected function signal(string $presenter, string $signal, array $params = [], string $action = 'default'): Response
    {
        return $this->runPresenter($presenter, ['action' => $action, 'do' => $signal] + $params);
    }

    /** Renders the whole page (layout included) and returns the HTML. */
    protected function renderPage(Response $response): string
    {
        $this->assertInstanceOf(TextResponse::class, $response);
        $source = $response->getSource();
        // renderToString() lives on the Latte implementation, not on the UI\Template interface
        $this->assertInstanceOf(Template::class, $source);

        return $source->renderToString();
    }
}
