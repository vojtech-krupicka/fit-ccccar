<?php
/**
 * Homepage.php
 *
 * Homepage kontroler - zobrazuje vicemene staticky obsah, jako hlavni stranku,
 * rady uzivatelum, obchodni podminky, seznam vsech prodejen a kontakty na 
 * jednotlive zamestnance v systemu.
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
/** ************************************************************************* */
/** Akce = offices                                                            */ 
/** ************************************************************************* */
if ($CC_SYSTEM->request->action == 'offices') {
    
    $CC_TEMPLATE->pageTitle = 'Pobočky';
    $CC_TEMPLATE->pageHeading = 'Pobočky';
    
    $item = new stdClass;
    $item->href = 'this';
    $item->params = array();
    $item->title = 'Pobočky';
    $CC_TEMPLATE->breadcrumb[] = $item;
    
    
    // Ziskame seznam vsech pobocek
    $list = $CC_SYSTEM->modelLoader->createList('CC_RentalOffice');
    $CC_TEMPLATE->rentalOfficeList = $list->find(array(), array('name ASC'));

    // Pro kazdou pobocku si vytahneme vsechny zamestnance
    $CC_TEMPLATE->employeeList = array();
    foreach ($CC_TEMPLATE->rentalOfficeList as $rentalOffice) {
        $CC_TEMPLATE->employeeList[$rentalOffice->getId()] = $list->findEmployees($rentalOffice->getId(), array('`e`.`surname` ASC', '`e`.`name`'));
    }
}