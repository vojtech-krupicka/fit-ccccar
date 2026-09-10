<?php
/**
 * config.php
 *
 * Konfigurace systemu
 *
 * @copyright 2014
 * @version 1.0.0
 */


// Autor systemu
define('CC_SYSTEM_AUTHOR', 'CCC Car');
define('CC_SYSTEM_EMAIL', 'info@ccccar.cz');

// Rok vytvoreni systemu
define('CC_SYSTEM_YEAR', '2014');

// Vychozi kodovani systemu
define('CC_SYSTEM_CHARSET', 'utf8');

// Vychozi casova zona
define('CC_SYSTEM_TIMEZONE', 'Europe/Prague');

// Definovani rezimu (devel vs. produkce)
define('CC_DEVEL_MODE', false);

// Nastaveni HTTP chybovych kodu
define('CC_HTTP_NOT_FOUND', '404');
define('CC_HTTP_SERVER_ERROR', '500');


// Nastaveni cest ve file systemu
$_SERVER["DOCUMENT_ROOT"] = "c:/www/program/www";
define('CC_DIR_ROOT', $_SERVER["DOCUMENT_ROOT"]);

define('CC_DIR_APP', CC_DIR_ROOT . '/app');
define('CC_DIR_CONTROLLER', CC_DIR_APP . '/controller');
define('CC_DIR_MODEL', CC_DIR_APP . '/model');
define('CC_DIR_VIEW', CC_DIR_APP . '/view');
define('CC_DIR_UTILS', CC_DIR_APP . '/utils');

define('CC_DIR_WWW', CC_DIR_ROOT . '/www');
define('CC_DIR_CSS', CC_DIR_WWW . '/css');
define('CC_DIR_IMG', CC_DIR_WWW . '/img');
define('CC_DIR_JS', CC_DIR_WWW . '/js');

define('CC_DIR_ADMIN_WWW', CC_DIR_WWW . '/admin/www');
define('CC_DIR_ADMIN_CSS', CC_DIR_ADMIN_WWW . '/css');
define('CC_DIR_ADMIN_IMG', CC_DIR_ADMIN_WWW . '/img');
define('CC_DIR_ADMIN_JS', CC_DIR_ADMIN_WWW . '/js');

// Nastaveni webovych cest aplikace
define('CC_PATH_ROOT', 'http://'.$_SERVER["HTTP_HOST"].'/program/www');
define('CC_PATH_ADMIN', 'http://'.$_SERVER["HTTP_HOST"].'/program/www/admin');

define('CC_PATH_CSS', CC_PATH_ROOT . '/css');
define('CC_PATH_IMG', CC_PATH_ROOT . '/img');
define('CC_PATH_JS', CC_PATH_ROOT . '/js');

define('CC_PATH_ADMIN_CSS', CC_PATH_ADMIN . '/css');
define('CC_PATH_ADMIN_IMG', CC_PATH_ADMIN . '/img');
define('CC_PATH_ADMIN_JS', CC_PATH_ADMIN . '/js');


// Pripojeni k DB
define('CC_DATABASE_SERVER', 'localhost');
define('CC_DATABASE_DBNAME', 'ccccar');
define('CC_DATABASE_USER', 'ccccar');
define('CC_DATABASE_PASSWORD', 'xxx');
define('CC_DATABASE_CHARSET', 'utf8');


// /////////////////////////////////////////////////////////////////////////////
// Dalsi nastaveni systemu
// /////////////////////////////////////////////////////////////////////////////


// Vychozi URL parametry
define('CC_DEFAULT_CONTROLLER', 'homepage');
define('CC_DEFAULT_ACTION', 'default');
define('CC_DEFAULT_SIGNAL', '');


// Informace o eshopu
define('CC_OWNER_NAME', 'CCC Car');
define('CC_OWNER_EMAIL', 'info@ccccar.cz');
  