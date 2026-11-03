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
        yield 'front home' => ['/', ['presenter' => 'Front:Home', 'action' => 'default']];
        yield 'front czech presenter and action' => ['/katalog/detail/5', ['presenter' => 'Front:Catalog', 'action' => 'detail', 'id' => '5']];
        yield 'front sign in' => ['/prihlaseni', ['presenter' => 'Front:SignIn', 'action' => 'default']];
        yield 'admin home' => ['/admin', ['presenter' => 'Admin:Home', 'action' => 'default']];
        yield 'admin czech presenter' => ['/admin/vozidla', ['presenter' => 'Admin:Vehicle', 'action' => 'default']];
        yield 'admin delete with id' => ['/admin/zakaznik/smazat/5', ['presenter' => 'Admin:Customer', 'action' => 'delete', 'id' => '5']];
    }

    public function testAdminIdMustBeNumeric(): void
    {
        $params = $this->router->match(new Request(new UrlScript('http://localhost/admin/zakaznik/smazat/abc', '/')));

        // the admin route refuses a non-numeric id, so it falls through to the front route (which is not a presenter 'admin')
        $this->assertTrue($params === null || ($params['presenter'] ?? null) !== 'Admin:Customer');
    }

    public function testUrlGeneration(): void
    {
        $refUrl = new UrlScript('http://localhost/', '/');

        $this->assertSame(
            'http://localhost/katalog/detail/5',
            $this->router->constructUrl(['presenter' => 'Front:Catalog', 'action' => 'detail', 'id' => '5'], $refUrl),
        );
    }
}
