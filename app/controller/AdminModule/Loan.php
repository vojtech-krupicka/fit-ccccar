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
    
    $CC_TEMPLATE->pageTitle = 'Správa výpůjček';
    $CC_TEMPLATE->pageHeading = 'Správa výpůjček';
    
    $item = new stdClass;
    $item->href = 'this';
    $item->params = array();
    $item->title = 'Správa výpůjček';
    $CC_TEMPLATE->breadcrumb[] = $item;
    
    // Vychozi hodnoty pro filtr
    $CC_TEMPLATE->filterForm = new stdClass;
    $CC_TEMPLATE->filterForm->errors = array();
    $CC_TEMPLATE->filterForm->hasFilter = array();
    
    $CC_TEMPLATE->filterForm->customer = null;
    if (isset($CC_SYSTEM->request->params['customer-id']) && !empty($CC_SYSTEM->request->params['customer-id'])) {
        $CC_TEMPLATE->filterForm->customer = (int)$CC_SYSTEM->request->params['customer-id'];
        $CC_TEMPLATE->filterForm->hasFilter['customer'] = true;
    }
    
    $CC_TEMPLATE->filterForm->loan = null;
    if (isset($CC_SYSTEM->request->params['loan-id']) && !empty($CC_SYSTEM->request->params['loan-id'])) {
        $CC_TEMPLATE->filterForm->loan = (int)$CC_SYSTEM->request->params['loan-id'];
        $CC_TEMPLATE->filterForm->hasFilter['loan'] = true;
    }
    
    
    /** ********************************************************************* */
    /** Signal = filterForm                                                   */ 
    /** ********************************************************************* */
    if ($CC_SYSTEM->request->signal == 'filterForm') {
        // Pokud nemame data v postu
        if (empty($CC_SYSTEM->request->post)) {
            CC_Utils::redirect('this');
        }
        
        // Vytahneme si data z postu
        $values = $CC_SYSTEM->request->post;
        
        
        // Pokud bylo odeslano tlacitke 'btnFilter' nastavime hodnoty
        if (isset($values['btnFilter'])) {
            $params = array();
            
            if (isset($values['customer']) && (int)$values['customer'] > 0) {
                $params['customer-id'] = (int)$values['customer'];
            }
            if (isset($values['loan']) && (int)$values['loan'] > 0) {
                $params['loan-id'] = (int)$values['loan'];
            }
            
            // Presmerujeme na stranku katalogu, nastavime filtrovaci parametry
            CC_Utils::redirect('this', $params);
        }
        // Bylo stisknuto jine tlacitko (zrusi filtry)
        else {
            CC_Utils::redirect('this', array());
        }
    }
    
    
    // Seznam vsech zakazniku
    $list = $CC_SYSTEM->modelLoader->createList('CC_Customer');
    foreach ($list->find(array(), array('surname ASC', 'name ASC')) as $item) {
        $CC_TEMPLATE->customerList[$item->getId()] = $item->getSurname() . ' ' . $item->getName();
    }
    
    
    // Vytvori instanci strankovace pro seznam vypujcek
    $CC_TEMPLATE->paginator = new CC_Paginator;
    $CC_TEMPLATE->paginator->setPage(isset($CC_SYSTEM->request->params['page']) ? $CC_SYSTEM->request->params['page'] : 1);
    $CC_TEMPLATE->paginator->setItemsPerPage(15);    
    
    // Pripravime filtr a razeni
    $filter = array();
    $sorter = array('reservedFrom DESC', 'reservedTo DESC', 'reservedAt DESC');
    
    // Nastavi filtry podle URL
    if ($CC_TEMPLATE->filterForm->customer) {
        $filter['customer_id'] = $CC_SYSTEM->request->params['customer-id'];
    }
    if ($CC_TEMPLATE->filterForm->loan) {
        $filter['loan_id'] = $CC_SYSTEM->request->params['loan-id'];
    }
    
    // Ziskame vsechny vypujcky pro daneho uzivatele
    $list = $CC_SYSTEM->modelLoader->createList('CC_Loan');
    $loans = $list->find($filter, $sorter, $CC_TEMPLATE->paginator->getItemsPerPage(), $CC_TEMPLATE->paginator->getOffset());
    
    // Nastavi celkovy pocet vypujcek a 
    $CC_TEMPLATE->paginator->setTotalCount($list->getTotalCount());
    
    // Prida vypujcky do sablony
    $CC_TEMPLATE->loanList = $loans;
}

/** ************************************************************************* */
/** Akce = detail                                                             */ 
/** ************************************************************************* */
if ($CC_SYSTEM->request->action == 'detail') {
    
    // Vychozi hodnoty pro formular
    $CC_TEMPLATE->closeForm = new stdClass;
    $CC_TEMPLATE->closeForm->distance = null;
    $CC_TEMPLATE->closeForm->rentalOffice = null;
    $CC_TEMPLATE->closeForm->errors = array();
    
    /** ********************************************************************* */
    /** Signal = closeForm                                                    */ 
    /** ********************************************************************* */
    if ($CC_SYSTEM->request->signal == 'closeForm') {
        // Pokud nemame data v postu
        if (empty($CC_SYSTEM->request->post)) {
            CC_Utils::redirect('this');
        }
        
        // Vytahneme si data z postu
        $loan = null;
        $values = $CC_SYSTEM->request->post;
        
        
        // Ziskame ID a pokusime se nacist vozidlo
        if (!isset($values['id']) || empty($values['id'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = 'Formulář obsahuje špatná data!';
            CC_Utils::redirect(':Admin:Loan:default');
        } else {
            $loan = $CC_SYSTEM->modelLoader->get('CC_Loan', (int)$values['id']);
            if (!$loan) {
                // Vypujcka nenalezena, presmerujeme
                $CC_TEMPLATE->flashMessages['danger'][] = 'Výpůjčka s id <strong>'.$values['id'].'</strong> nebyla nalezena!';
                CC_Utils::redirect(':Admin:Loan:default');
            }
        }
        
        
        // Kontrola, zda jsou zadane udaje spravne
        if (empty($values['distance'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = 'Není zadána <strong>ujetá vzdálenost</strong>!';
            $CC_TEMPLATE->closeForm->errors['distance'] = true;
        }
        if (empty($values['rentalOffice']) || !CC_RentalOffice::exists($CC_SYSTEM->dbconn, $values['rentalOffice'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = 'Není vybrána <strong>pobočka</strong>!';
            $CC_TEMPLATE->closeForm->errors['rentalOffice'] = true;
        }
        
        
        // Pokud doslo pri zpracovani formulare k chybe!
        if (count($CC_TEMPLATE->closeForm->errors) > 0) {
            // Predame hodnoty do formulare
            $CC_SYSTEM->request->params['id'] = $values['id'];
            $CC_TEMPLATE->closeForm->distance = $values['distance'];
            $CC_TEMPLATE->closeForm->rentalOffice = $values['rentalOffice'];
        } else {
            // Nastavi vypujcku
            $loan->setEndAt(new DateTime);
            $loan->setRecipient($CC_SYSTEM->admin);
            $loan->setDistance($values['distance']);
            
            // Nastavi automobil
            $vehicle = $loan->getVehicle();
            $vehicle->setRentalOffice($CC_SYSTEM->modelLoader->get('CC_RentalOffice', $values['rentalOffice']));
            $vehicle->setMileage($vehicle->getMileage() + $loan->getDistance());
            
            // Spocitame celkovou cenu
            $totalPrice = 0.0;
            $totalDays = abs((int)$loan->getEndAt()->diff($loan->getStartAt())->format('%R%a')) + 1;
            $penaltyDays = $loan->getEndAt() > $loan->getReservedTo() ? abs((int)$loan->getEndAt()->diff($loan->getReservedTo())->format('%R%a')) : 0;
            
            $costPerUnit = $vehicle->getCostPerUnit() * $loan->getDistance();
            $costPerDay = $vehicle->getCostPerDay() * $totalDays;
            $penaltyCost = $penaltyDays * 399.0;
            $totalPrice = $costPerUnit + $costPerDay + $penaltyCost;
            
            // Nastavime cenu
            $loan->setPrice($totalPrice);
            
            // Zahajime transakci
            $CC_SYSTEM->dbconn->begin();

            // Ulozime
            if ($loan->persist() && $vehicle->persist()) {
                $CC_SYSTEM->dbconn->commit();
                $CC_TEMPLATE->flashMessages['success'][] = "Výpůjčka byla úspěšně uzavřena.";
            } else {
                $CC_SYSTEM->dbconn->rollback();
                $CC_TEMPLATE->flashMessages['danger'][] = "Výpůjčku nelze zavřít, vyskytla se neočekávaná chyba! Opakujte akci znovu.";
            }
            
            // Presmerujeme na detail vypujcky
            CC_Utils::redirect('this', array('id' => $values['id']));
        }
    }
        
    
    $loan = null;
    
    // Pokud je zadano ID vypujcky
    if (isset($CC_SYSTEM->request->params['id']) && !empty($CC_SYSTEM->request->params['id'])) {
        // Zkusime najit vypujcku
        $loan = $CC_SYSTEM->modelLoader->get('CC_Loan', (int)$CC_SYSTEM->request->params['id']);
        if (!$loan) {
            // Vypujcka nenalezena, presmerujeme
            $CC_TEMPLATE->flashMessages['danger'][] = 'Výpůjčka s id <strong>'.$CC_SYSTEM->request->params['id'].'</strong> nebyla nalezena!';
            CC_Utils::redirect(':Admin:Loan:default');
        }
    } else {
        // ID neni zadano, presmerujeme
        $CC_TEMPLATE->flashMessages['danger'][] = 'Výpůjčka nebyla nalezena!';
        CC_Utils::redirect(':Admin:Loan:default');
    }
    
    // Mame vozidlo
    if ($loan) {
        
        /** ********************************************************************* */
        /** Signal = open                                                         */ 
        /** ********************************************************************* */
        if ($CC_SYSTEM->request->signal == 'open') {
            // Pokud nemame ID v URL
            if (!isset($CC_SYSTEM->request->params['id']) || empty($CC_SYSTEM->request->params['id'])) {
                CC_Utils::redirect('this', $CC_SYSTEM->request->params);
            }

            // ID mame, najdeme vypujcku a z parametru odebereme ID
            $loan = $CC_SYSTEM->modelLoader->get('CC_Loan', $CC_SYSTEM->request->params['id']);
            unset($CC_SYSTEM->request->params['id']);

            // ID mame, najdeme vypujcku a z parametru odebereme ID
            if (!$loan) {
                // Vypujcka neexistuje
                $CC_TEMPLATE->flashMessages['danger'][] = "Výpůjčka nebyla nalezena!";
                CC_Utils::redirect('this', $CC_SYSTEM->request->params);
            } else if ($loan->getStartAt() || $loan->getEndAt() || $loan->isDeleted()) {
                // Vypujcka je jiz zapujcena nebo byla zrusena
                $CC_TEMPLATE->flashMessages['danger'][] = "Výpůjčku nelze potvrdit, výpůjčka je již aktivní nebo byla již zrušena!";
                CC_Utils::redirect('this', $CC_SYSTEM->request->params);
            } else {
                // Vypujcce nastive aktualni datum a zamestnance ktery vypujcku provedl
                $loan->setStartAt(new DateTime);
                $loan->setBorrower($CC_SYSTEM->admin);

                // Vozidlu nastavime aktualni pobocku na ktere si zakaznik pral vozidlo vypujcit
                $vehicle = $loan->getVehicle();
                $vehicle->setRentalOffice($loan->getRentalOffice());

                // Zahajime transakci
                $CC_SYSTEM->dbconn->begin();

                // Ulozime
                if ($loan->persist() && $vehicle->persist()) {
                    $CC_SYSTEM->dbconn->commit();
                    $CC_TEMPLATE->flashMessages['success'][] = "Výpůjčka byla úspěšně potvrzena.";
                } else {
                    $CC_SYSTEM->dbconn->rollback();
                    $CC_TEMPLATE->flashMessages['danger'][] = "Výpůjčku nelze potvrdit, vyskytla se neočekávaná chyba! Opakujte akci znovu.";
                }

                // Presmerujeme
                CC_Utils::redirect('this', array('id' => $values['id']));
            }
        }
        /** ********************************************************************* */
        /** Signal = storno                                                       */ 
        /** ********************************************************************* */
        else if ($CC_SYSTEM->request->signal == 'storno') {
            // Pokud nemame ID v URL
            if (!isset($CC_SYSTEM->request->params['id']) || empty($CC_SYSTEM->request->params['id'])) {
                CC_Utils::redirect('this', $CC_SYSTEM->request->params);
            }

            // ID mame, najdeme vypujcku a z parametru odebereme ID
            $loan = $CC_SYSTEM->modelLoader->get('CC_Loan', $CC_SYSTEM->request->params['id']);
            unset($CC_SYSTEM->request->params['id']);

            // ID mame, najdeme vypujcku a z parametru odebereme ID
            if (!$loan) {
                // Vypujcka neexistuje
                $CC_TEMPLATE->flashMessages['danger'][] = "Výpůjčka nebyla nalezena!";
                CC_Utils::redirect('this', $CC_SYSTEM->request->params);
            } else if ($loan->getStartAt() || $loan->getEndAt() || $loan->isDeleted()) {
                // Vypujcka je jiz zapujcena nebo byla zrusena
                $CC_TEMPLATE->flashMessages['danger'][] = "Výpůjčku nelze zrušit, výpůjčka je již aktivní nebo byla již zrušena!";
                CC_Utils::redirect('this', $CC_SYSTEM->request->params);
            } else {
                // Zrusime vypujcku
                $loan->setDeleted(true);

                // Ulozime
                if ($loan->persist()) {
                    $CC_TEMPLATE->flashMessages['success'][] = "Výpůjčka byla úspěšně zrušena.";
                } else {
                    $CC_TEMPLATE->flashMessages['danger'][] = "Výpůjčku nelze zrušit, vyskytla se neočekávaná chyba! Opakujte akci znovu.";
                }

                // Presmerujeme
                CC_Utils::redirect('this', array('id' => $values['id']));
            }
        }
        
        
        $CC_TEMPLATE->pageTitle = 'Detail výpůjčky';
        $CC_TEMPLATE->pageHeading = 'Detail výpůjčky';

        $item = new stdClass;
        $item->href = ':Admin:Loan:default';
        $item->params = array();
        $item->title = 'Správa výpůjček';
        $CC_TEMPLATE->breadcrumb[] = $item;

        $item = new stdClass;
        $item->href = 'this';
        $item->params = array();
        $item->title = 'Detail výpůjčky';
        $CC_TEMPLATE->breadcrumb[] = $item;
        
        // Pridame vozidlo do sablony
        $CC_TEMPLATE->loan = $loan;
        
        // Seznam pobocek pro rezervaci vozidla
        $CC_TEMPLATE->rentalOfficeList = array();
        $list = $CC_SYSTEM->modelLoader->createList('CC_RentalOffice');
        foreach ($list->find() as $item) {
            $CC_TEMPLATE->rentalOfficeList[$item->getId()] = $item->getName();
        }
        
        $labelClass = '';
        $labelText = '';
        $panelClass = '';
        $toolStatus = '';
        
        // Ziskame typ vypujcky pro label a vykresleni tlacitek
        if (!$loan->isDeleted()) {
            if (!$loan->getStartAt() && !$loan->getEndAt()) {
                $labelClass = 'danger';
                $labelText = 'Nová';
                $loanStatus = 'new';
            } else if ($loan->getStartAt() && !$loan->getEndAt()) {
                $labelClass = 'warning';
                $labelText = 'Aktivní';
                $loanStatus = 'opened';
            } else {
                $labelClass = 'info';
                $labelText = 'Uzavřená';
                $loanStatus = 'closed';
            }
        } else if ($loan->isDeleted()) {
            $labelClass = 'default';
            $labelText = 'Stornovaná';
            $loanStatus = 'deleted';
        }
        
        $CC_TEMPLATE->labelClass = $labelClass;
        $CC_TEMPLATE->labelText = $labelText;
        $CC_TEMPLATE->loanStatus = $loanStatus;
        
    }
    
}