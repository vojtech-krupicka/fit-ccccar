<?php declare(strict_types=1);

namespace Tests;

use App\Core\Authentication\Identity;
use Nette\Application\IPresenterFactory;
use Nette\Application\Request;
use Nette\Application\Response;
use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Security\User;

/**
 * Base class for presenter tests: runs a presenter from the real DI container
 * (against the test database) and returns its response.
 *
 * Every test starts as a guest; call loginAs() to act as a signed-in user. The login lives in memory
 * (no PHP session), so it never leaks between tests.
 */
abstract class PresenterTestCase extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->useUser(new User(new ArrayUserStorage()));
    }

    protected function loginAs(Identity $identity): void
    {
        $storage = new ArrayUserStorage();
        $storage->saveAuthentication($identity);
        $this->useUser(new User($storage));
    }

    /** Simulates an expired "stay signed in" login: the identity is still stored, but isLoggedIn() is false. */
    protected function keepIdentityButLogOut(Identity $identity): void
    {
        $storage = new ArrayUserStorage();
        $storage->saveAuthentication($identity);
        $storage->clearAuthentication(false);
        $this->useUser(new User($storage));
    }

    /** Replaces the container's user so that presenters created afterwards use it. */
    private function useUser(User $user): void
    {
        $container = self::container();
        $container->removeService('security.user');
        $container->addService('security.user', $user);
    }

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
