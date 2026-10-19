<?php declare(strict_types=1);

namespace App\Core;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette;

final class RouterFactory
{
    use Nette\StaticClass;

    public static function createRouter(): RouteList
    {
        $router = new RouteList;

        $adminRouter = new RouteList('Admin');
        $adminRouter->addRoute('admin/index.php', 'Home:default', Route::ONE_WAY);
        $adminRouter->addRoute('admin/<presenter>[/<action>[/<id \d+(?:-[a-z-]+)?>]]', array(
            'presenter' => array(
                Route::Value => 'Home',
                Route::FilterTable => array(
                    'uvod' => 'Home',
                    'prihlaseni' => 'SignIn',
                    'odhlaseni' => 'SignOut',
                    'zamestnanec' => 'Employee',
                ),
            ),
            'action' => array(
                Route::Value => 'default',
                Route::FilterTable => array(
                    'vychozi' => 'default',
                    'profil' => 'profile',
                ),
            ),
            'id' => null,
        ));

        $frontRouter = new RouteList('Front');
        $frontRouter->addRoute('index.php', 'Home:default', Route::ONE_WAY);
        $frontRouter->addRoute('<presenter>[/<action>[/<id>]]', array(
            'presenter' => array(
                Route::Value => 'Home',
                Route::FilterTable => array(
                    'uvod' => 'Home',
                    'prihlaseni' => 'SignIn',
                    'registrace' => 'SignUp',
                    'odhlaseni' => 'SignOut',
                    'zakaznik' => 'Customer',
                    'katalog' => 'Catalog',
                ),
            ),
            'action' => array(
                Route::Value => 'default',
                Route::FilterTable => array(
                    'vychozi' => 'default',
                    'profil' => 'profile',
                    'detail' => 'detail',
                    'pobocky' => 'offices',
                ),
            ),
            'id' => null,
        ));

        $router->add($adminRouter);
        $router->add($frontRouter);

        return $router;
    }
}
