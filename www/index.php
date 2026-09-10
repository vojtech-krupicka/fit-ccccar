<?php
/**
 * index.php
 *
 * Hlavni skript systemu.
 *
 * @copyright 2014
 * @version 1.0.0
 */


// Vypnuti zobrazovani chybovych hlasek
error_reporting(15);
ini_set('display_errors', 1);

// Kontrolni mereni casu - zacatek zpracovani skriptu
$timeStart = microtime(true);

// Nacteme konfiguraci
require('../app/config.php');

// Nacteme zavadec aplikace
require(CC_DIR_APP . '/bootstrap.php');

// Vse v poradku, zkusime najit odpovidajici kontroller
if (!isset($CC_ERROR) || count($CC_ERROR) == 0) {
    // Zkusime najit kontroller, ten lezi na 'app/controller/<MODULES>/<CONTROLLER>.php'
    $controller = CC_DIR_CONTROLLER . '/' . $CC_SYSTEM->template->module . '/' . ucfirst($CC_SYSTEM->request->controller) . '.php';
    if (!is_file($controller)) {
        // Pokud kontroller neexistuje, zkusime najit vychozi kontroler
        $controller = CC_DIR_CONTROLLER . '/' . $CC_SYSTEM->template->module . '/' . CC_DEFAULT_CONTROLLER . '.php';
        if (!is_file($controller)) {
            // Pokud ani ten neexistuje - chyba
            $CC_ERROR[CC_HTTP_NOT_FOUND][] = 'Požadovaná stránka neexituje!';
        }
    }
    
    // Mame kontroler?
    if (!isset($CC_ERROR) || count($CC_ERROR) == 0) {
        // Natahneme kontroler, ktery pripravi data pro sablonu
        require_once $controller;
        
        // Kontroler probehnul ok?
        if (!isset($CC_ERROR) || count($CC_ERROR) == 0) {
            // Pripravime sablonu a layout
            $layout = CC_DIR_VIEW . '/' . $CC_SYSTEM->template->module . '/@' .  $CC_SYSTEM->template->layout . '.phtml';
            $view = CC_DIR_VIEW . '/' . $CC_SYSTEM->template->module . '/' .  ucfirst($CC_SYSTEM->request->controller) . '.' . lcfirst($CC_SYSTEM->template->view) . '.phtml';

            // Otestujeme, zda oba soubory existuji
            if (!is_file($layout) || !is_file($view)) {
                // Layout nebo pohled neexistuji - chyba
                $CC_ERROR[CC_HTTP_NOT_FOUND][] = 'Požadovaná stránka neexituje!';
            } else {
                // Cestu k pohledu si ulozime do CC_TEMPLATE (bude includovana z layoutu).
                $CC_TEMPLATE->view = $view;
                require_once $layout;
            }
        }
    }
}


// Pokud kdekoliv doslo k chybe, zobrazime chybovou stranku.
if (isset($CC_ERROR) && count($CC_ERROR) > 0) {
    // Osetreni chyby - natahne error stranku
    $CC_TEMPLATE->error = $CC_ERROR;
    require_once CC_DIR_VIEW . '/@error.phtml';
}


// Vytiskneme si info o vsech sessions
CC_Utils::sessionDump();

// Info o prihlaseneme uzivateli
dump($CC_SYSTEM->user);

// Obsah model loaderu
dump($CC_SYSTEM->modelLoader);

// Kontrolni mereni casu - konec zpracovani skriptu
$timeEnd = microtime(true);
$time = $timeEnd - $timeStart;
dump("Doba vykonani skriptu: " . $time);
