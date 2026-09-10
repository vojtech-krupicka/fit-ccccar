<?php
/**
 * AdminModule/Customer.php
 *
 * customer kontroler - zobrazuje seznam vsech registrovanych uzivatelu.
 *
 * @copyright 2014
 * @version 1.0.0
 */


/** ************************************************************************* */
/** Akce = default                                                            */ 
/** ************************************************************************* */
if ($CC_SYSTEM->request->action == 'default') {
    
    $CC_TEMPLATE->pageTitle = 'Správa zákazníků';
    $CC_TEMPLATE->pageHeading = 'Správa zákazníků';
    
    $item = new stdClass;
    $item->href = 'this';
    $item->params = array();
    $item->title = 'Správa zákazníků';
    $CC_TEMPLATE->breadcrumb[] = $item;
    
    
    // Vytvori instanci strankovace pro seznam vypujcek
    $CC_TEMPLATE->paginator = new CC_Paginator;
    $CC_TEMPLATE->paginator->setPage(isset($CC_SYSTEM->request->params['page']) ? $CC_SYSTEM->request->params['page'] : 1);
    $CC_TEMPLATE->paginator->setItemsPerPage(10);    
    
    // Ziskame seznam vsech pobocek
    $list = $CC_SYSTEM->modelLoader->createList('CC_Customer');
    $CC_TEMPLATE->customerList = $list->find(array(), array('surname ASC', 'name ASC'), $CC_TEMPLATE->paginator->getItemsPerPage(), $CC_TEMPLATE->paginator->getOffset());
    
    // Nastavi celkovy pocet vypujcek a 
    $CC_TEMPLATE->paginator->setTotalCount($list->getTotalCount());
}