<?php
/**
 * config.php
 *
 * Zavadec aplikace
 *
 * @copyright 2014
 * @version 1.0.0
 */


/**
 * Funkce pro nacteni trid v pripade jejich pouziti.
 * @param className Jmeno tridy, ktera ma byt nactena
 * @return Vraci true, pokud trida byla nactena, jinak false
 */
function __autoload($className)
{
    if(is_file(CC_DIR_MODEL . '/' . $className . ".php")) {
        require_once(CC_DIR_MODEL . '/' . $className . ".php");
    } else if(is_file(CC_DIR_UTILS . '/' . $className . ".php")) {
        require_once(CC_DIR_UTILS . '/' . $className . ".php");
    } else {
        return false;
    }
    
    return true;
}


/**
 * Pomocna funkce pro rychlejsi vypis promennych.
 * @param mixed $var
 * @return mixed
 * @tracySkipLocation
 */
function dump($var)
{
    if (!CC_DEVEL_MODE) {
        return $var;
    }
    
    foreach (func_get_args() as $arg) {
        echo '<pre>';
        var_dump($arg);
        echo '</pre>';
    }
    
    return $var;
}


/**
 * Nastartuje session.
 */
session_start();


/**
 * Nastaveni vychozi casove zony.
 */
date_default_timezone_set(CC_SYSTEM_TIMEZONE);


/**
 * Pole pro predavani dat do sablony.
 * @var stdClass
 */
$CC_TEMPLATE = new stdClass;


/**
 * Globalni promenna systemu.
 * @var stdClass
 */
$CC_SYSTEM = new stdClass;


/**
 * Globalni promenna pro chyby systemu.
 * var array[]
 */
$CC_ERROR = array();


// Vytvori globalni prostor pro ukladani session v aplikaci
if (!isset($_SESSION['ccccar'])) {
    $_SESSION['ccccar'] = array();
}


// Vytvori nove pripojeni k databazi
$CC_SYSTEM->dbconn = new CC_Database();
if(!$CC_SYSTEM->dbconn->connect(CC_DATABASE_SERVER, CC_DATABASE_DBNAME, CC_DATABASE_USER, CC_DATABASE_PASSWORD, CC_DATABASE_CHARSET)) {
    $CC_ERROR[CC_HTTP_SERVER_ERROR][] = 'Nepodarilo se pripojit k databazi.';
}


// Vytvori model loader
$CC_SYSTEM->modelLoader = new CC_ModelLoader($CC_SYSTEM->dbconn);


// Vytvori request z aktualni URL
$CC_SYSTEM->request = new stdClass;
$CC_SYSTEM->request->url = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$CC_SYSTEM->request->params = array();
$CC_SYSTEM->request->post = array();
$CC_SYSTEM->request->files = array();
$CC_SYSTEM->request->module = '';
$CC_SYSTEM->request->controller = CC_DEFAULT_CONTROLLER;
$CC_SYSTEM->request->action = CC_DEFAULT_ACTION;
$CC_SYSTEM->request->signal = CC_DEFAULT_SIGNAL;

// Z URL vytahne modul
$parts = explode('?', $CC_SYSTEM->request->url);
if (isset($parts[0])) {
    $module = preg_replace('~^' . CC_PATH_ROOT . '~', '', $parts[0]);
    
    // Vyhodi posledni prvek ze seznamu modulu, pokud se jedna o jmeno scriptu
    $filteredParts = array();
    $moduleParts = explode('/', $module);
    foreach ($moduleParts as $m) {
        $p = strpos($m, '.');
        if (!empty($m) && $p === false) {
            $filteredParts[] = $m;
        }
    }
    
    $CC_SYSTEM->request->module = join(':', $filteredParts);
}

// Z URL vytahne aktualni kontroler
if (isset($_GET['c']) && !empty($_GET['c'])) {
    $CC_SYSTEM->request->controller = $_GET['c'];
    unset($_GET['c']);
}
// Z URL vytahne aktualni akci
if (isset($_GET['a']) && !empty($_GET['a'])) {
    $CC_SYSTEM->request->action = $_GET['a'];
    unset($_GET['a']);
}
// Z URL vytahne aktualni signal
if (isset($_GET['s']) && !empty($_GET['s'])) {
    $CC_SYSTEM->request->signal = $_GET['s'];
    unset($_GET['s']);
}

// Z URL vytahne vsechny ostatni parametry
$CC_SYSTEM->request->params = $_GET;
unset($_GET);

// Pokud je k dispozici i pole POST, pripadne FILES, ulozi jeho obsah do requestu
if (!empty($_POST)) {
    $CC_SYSTEM->request->post = $_POST;
    unset($_POST);
}
if (!empty($_FILES)) {
    $CC_SYSTEM->request->files = $_FILES;
    unset($_FILES);
}


// Vytvori uzivatele, pokud existuje v session
$CC_SYSTEM->user = null;
$CC_SYSTEM->admin = null;

// Vytvori zakaznika
if (CC_Utils::sessionExists('customer-id') && CC_Utils::sessionGet('customer-id') > 0) {
    $CC_SYSTEM->user = $CC_SYSTEM->modelLoader->get('CC_Customer', CC_Utils::sessionGet('customer-id'));

    // Zkusime nacist zakaznika, pokud se nepodari, zrusime session i uzivatele
    if (!$CC_SYSTEM->user) {
        $CC_SYSTEM->user = null;
        CC_Utils::sessionSet('customer-id', null);
    } else if ($CC_SYSTEM->admin) {
        // Pokud existuje zamestnance, zrusime ho a zrusime i session (odhlasi zakaznika)
        CC_Utils::sessionSet('employee-id', null);
        $CC_SYSTEM->modelLoader->detach($CC_SYSTEM->admin);
    }
}
// Vytvori zamestnance
if (CC_Utils::sessionExists('employee-id') && CC_Utils::sessionGet('employee-id') > 0) {
    $CC_SYSTEM->admin = $CC_SYSTEM->modelLoader->get('CC_Employee', CC_Utils::sessionGet('employee-id'));
    
    // Zkusime nacist zamestnance, pokud se nepodari, zrusime session i uzivatele
    if (!$CC_SYSTEM->admin) {
        $CC_SYSTEM->admin = null;
        CC_Utils::sessionSet('employee-id', null);
    } else if ($CC_SYSTEM->user) {
        // Pokud existuje zakaznik, zrusime ho a zrusime i session (odhlasi zakaznika)
        CC_Utils::sessionSet('customer-id', null);
        $CC_SYSTEM->modelLoader->detach($CC_SYSTEM->user);
    }
}


// Nastaveni layoutu a pohledu
$CC_SYSTEM->template = new stdClass;

// Info o sablone (layout a view), lze zmenit v kontroleru
$CC_SYSTEM->template->layout = 'layout';
$CC_SYSTEM->template->view = lcfirst($CC_SYSTEM->request->action);

// Cesta do modulu
$CC_SYSTEM->template->module = '';
if ($CC_SYSTEM->request->module != '') {
    // Modul je retezec oddeleny ':' coz je oddelovac modulu.
    // Rozdelime retezec podle ':', ke kazdemu modulu pridame slovo 'Module'
    // a opet spojime, tentokrat '/' pro cestu ve file systemu
    $CC_SYSTEM->template->module = join('/', array_map(function($item) {
                return $item . 'Module';
            }, array_map("ucfirst", explode(':', $CC_SYSTEM->request->module))));
}


// /////////////////////////////////////////////////////////////////////////////
// Nastaveni vychozich promennych do sablony
// /////////////////////////////////////////////////////////////////////////////


// Cesty do hlavnich slozek aplikace pristupne pres HTTP adresu
$CC_TEMPLATE->paths = new stdClass;
$CC_TEMPLATE->paths->root = CC_PATH_ROOT;
$CC_TEMPLATE->paths->images = CC_PATH_IMG;
$CC_TEMPLATE->paths->css = CC_PATH_CSS;
$CC_TEMPLATE->paths->js = CC_PATH_JS;

$CC_TEMPLATE->paths->admin = new stdClass;
$CC_TEMPLATE->paths->admin->root = CC_PATH_ADMIN;
$CC_TEMPLATE->paths->admin->images = CC_PATH_ADMIN_IMG;
$CC_TEMPLATE->paths->admin->css = CC_PATH_ADMIN_CSS;
$CC_TEMPLATE->paths->admin->js = CC_PATH_ADMIN_JS;


// Prihlaseny uzivatel 
$CC_TEMPLATE->user = $CC_SYSTEM->user;
$CC_TEMPLATE->admin = $CC_SYSTEM->admin;

// Titulek, hlavni nadpis stranky a drobeckova navigace
$CC_TEMPLATE->pageTitle = '';
$CC_TEMPLATE->pageHeading = '';
$CC_TEMPLATE->breadcrumb = array();

// Flash zpravicky, pokud jsou v session, vytahneme si je
if (CC_Utils::sessionExists('flash-messages')) {
    $CC_TEMPLATE->flashMessages = CC_Utils::sessionGet('flash-messages');
    
    // Zpravicky odstranime ze session aby pri dalsim reloadu nezobrazovali
    CC_Utils::sessionSet('flash-messages', null);
} else {
    $CC_TEMPLATE->flashMessages = array(
        'info' => array(),
        'success' => array(),
        'warning' => array(),
        'danger' => array(),
    );
}
