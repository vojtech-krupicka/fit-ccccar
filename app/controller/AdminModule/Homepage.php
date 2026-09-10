<?php
/**
 * AdminModule/Homepage.php
 *
 * AdminModule/Homepage kontroler - hlavni stranka administrace.
 *
 * @copyright 2014
 * @version 1.0.0
 */

/** ************************************************************************* */
/** Akce = default                                                            */ 
/** ************************************************************************* */
if ($CC_SYSTEM->request->action == 'default') {

    $CC_TEMPLATE->pageHeading = 'Vítejte';

}