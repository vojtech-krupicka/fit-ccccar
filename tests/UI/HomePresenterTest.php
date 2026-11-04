<?php declare(strict_types=1);

namespace Tests\UI;

use Nette\Application\Responses\TextResponse;
use Tests\PresenterTestCase;

final class HomePresenterTest extends PresenterTestCase
{
    public function testTheFrontHomePageRenders(): void
    {
        $response = $this->runPresenter('Front:Home', ['action' => 'default']);

        $this->assertInstanceOf(TextResponse::class, $response);
        $html = $this->renderPage($response);
        $this->assertStringContainsString('CCC Car', $html);
    }

    public function testTheAdminHomeRedirectsGuestsToSignIn(): void
    {
        $response = $this->runPresenter('Admin:Home', ['action' => 'default']);

        $this->assertInstanceOf(\Nette\Application\Responses\RedirectResponse::class, $response);
        $this->assertStringContainsString('prihlaseni', $response->getUrl());
    }
}
