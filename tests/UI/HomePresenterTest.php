<?php declare(strict_types=1);

namespace Tests\UI;

use Nette\Application\Responses\TextResponse;
use Tests\PresenterTestCase;

final class HomePresenterTest extends PresenterTestCase
{
    public function testTheWelcomePageRenders(): void
    {
        $response = $this->runPresenter('Home', ['action' => 'default']);

        $this->assertInstanceOf(TextResponse::class, $response);
        $html = $this->renderPage($response);
        $this->assertStringContainsString('<title>It works | Nette Skeleton</title>', $html);
        $this->assertStringContainsString('It works', $html);
    }
}
