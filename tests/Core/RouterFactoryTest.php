<?php declare(strict_types=1);

namespace Tests\Core;

use App\Core\RouterFactory;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use Nette\Routing\Router;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(RouterFactory::class)]
final class RouterFactoryTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = RouterFactory::createRouter();
    }

    /**
     * @param array<string, mixed> $expected
     */
    #[DataProvider('provideMatchingUrls')]
    public function testMatch(string $path, array $expected): void
    {
        $params = $this->router->match(new Request(new UrlScript("http://localhost$path", '/')));

        $this->assertNotNull($params, "No route matched '$path'");
        foreach ($expected as $key => $value) {
            $this->assertSame($value, $params[$key] ?? null, "Parameter '$key' of '$path'");
        }
    }

    /**
     * @return iterable<string, array{string, array<string, mixed>}>
     */
    public static function provideMatchingUrls(): iterable
    {
        yield 'home' => ['/', ['presenter' => 'Home', 'action' => 'default']];
        yield 'explicit action' => ['/home/default', ['presenter' => 'Home', 'action' => 'default']];
        yield 'id parameter' => ['/product/show/5', ['presenter' => 'Product', 'action' => 'show', 'id' => '5']];
    }

    public function testUrlGeneration(): void
    {
        $refUrl = new UrlScript('http://localhost/', '/');

        $this->assertSame(
            'http://localhost/product/show/5',
            $this->router->constructUrl(['presenter' => 'Product', 'action' => 'show', 'id' => '5'], $refUrl),
        );
    }
}
