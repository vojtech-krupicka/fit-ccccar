<?php
/**
 * Catalog.php
 *
 * Catalog kontroler - zobrazuje seznam vsech automobilu v systemu, umoznuje
 * jejich filtrovani, razeni a strankovani. Zobrazuje detail o kazdem autu.
 *
 * @copyright 2014
 * @version 1.0.0
 */


/** ************************************************************************* */
/** Akce = default                                                            */ 
/** ************************************************************************* */
if ($CC_SYSTEM->request->action == 'default') {

    // Nastavi titulek a nadpis stranky
    $CC_TEMPLATE->pageTitle = 'Katalog vozidel';
    $CC_TEMPLATE->pageHeading = 'Katalog vozidel';
    
    // Prida polozku do drobeckove navigace
    $item = new stdClass;
    $item->href = 'this';
    $item->params = array();
    $item->title = 'Katalog vozidel';
    $CC_TEMPLATE->breadcrumb[] = $item;
    
    // Vychozi hodnoty pro filtr
    $CC_TEMPLATE->filterForm = new stdClass;
    $CC_TEMPLATE->filterForm->errors = array();
    $CC_TEMPLATE->filterForm->hasFilter = array();
    
    $CC_TEMPLATE->filterForm->rentalOffice = null;
    if (isset($CC_SYSTEM->request->params['ro_id']) && !empty($CC_SYSTEM->request->params['ro_id'])) {
        $CC_TEMPLATE->filterForm->rentalOffice = (int)$CC_SYSTEM->request->params['ro_id'];
        $CC_TEMPLATE->filterForm->hasFilter['rentalOffice'] = true;
    }
    
    $CC_TEMPLATE->filterForm->vehicleFuelType = null;
    if (isset($CC_SYSTEM->request->params['vft_id']) && !empty($CC_SYSTEM->request->params['vft_id'])) {
        $CC_TEMPLATE->filterForm->vehicleFuelType = (int)$CC_SYSTEM->request->params['vft_id'];
        $CC_TEMPLATE->filterForm->hasFilter['vehicleFuelType'] = true;
    }
    
    $CC_TEMPLATE->filterForm->vehicleColor = null;
    if (isset($CC_SYSTEM->request->params['vc_id']) && !empty($CC_SYSTEM->request->params['vc_id'])) {
        $CC_TEMPLATE->filterForm->vehicleColor = (int)$CC_SYSTEM->request->params['vc_id'];
        $CC_TEMPLATE->filterForm->hasFilter['vehicleColor'] = true;
    }
    
    $CC_TEMPLATE->filterForm->costPerDayFrom = null;
    if (isset($CC_SYSTEM->request->params['costPerDayFrom']) && !empty($CC_SYSTEM->request->params['costPerDayFrom'])) {
        $CC_TEMPLATE->filterForm->costPerDayFrom = (float)$CC_SYSTEM->request->params['costPerDayFrom'];
        $CC_TEMPLATE->filterForm->hasFilter['costPerDayFrom'] = true;
    }
    
    $CC_TEMPLATE->filterForm->costPerDayTo = null;
    if (isset($CC_SYSTEM->request->params['costPerDayTo']) && !empty($CC_SYSTEM->request->params['costPerDayTo'])) {
        $CC_TEMPLATE->filterForm->costPerDayTo = (float)$CC_SYSTEM->request->params['costPerDayTo'];
        $CC_TEMPLATE->filterForm->hasFilter['costPerDayTo'] = true;
    }
    
    $CC_TEMPLATE->filterForm->costPerUnitFrom = null;
    if (isset($CC_SYSTEM->request->params['costPerUnitFrom']) && !empty($CC_SYSTEM->request->params['costPerUnitFrom'])) {
        $CC_TEMPLATE->filterForm->costPerUnitFrom = (float)$CC_SYSTEM->request->params['costPerUnitFrom'];
        $CC_TEMPLATE->filterForm->hasFilter['costPerUnitFrom'] = true;
    }
    
    $CC_TEMPLATE->filterForm->costPerUnitTo = null;
    if (isset($CC_SYSTEM->request->params['costPerUnitTo']) && !empty($CC_SYSTEM->request->params['costPerUnitTo'])) {
        $CC_TEMPLATE->filterForm->costPerUnitTo = (float)$CC_SYSTEM->request->params['costPerUnitTo'];
        $CC_TEMPLATE->filterForm->hasFilter['costPerUnitTo'] = true;
    }
    
    $CC_TEMPLATE->filterForm->vehicleAvailability = null;
    if (isset($CC_SYSTEM->request->params['vehicleAvailability']) && !empty($CC_SYSTEM->request->params['vehicleAvailability'])) {
        $CC_TEMPLATE->filterForm->vehicleAvailability = $CC_SYSTEM->request->params['vehicleAvailability'];
        $CC_TEMPLATE->filterForm->hasFilter['vehicleAvailability'] = true;
    }
    
    $CC_TEMPLATE->filterForm->sorting = null;
    if (isset($CC_SYSTEM->request->params['sorting']) && !empty($CC_SYSTEM->request->params['sorting'])) {
        $CC_TEMPLATE->filterForm->sorting = $CC_SYSTEM->request->params['sorting'];
    }
    
    $CC_TEMPLATE->filterForm->sortingType = null;
    if (isset($CC_SYSTEM->request->params['sortingType']) && !empty($CC_SYSTEM->request->params['sortingType'])) {
        $CC_TEMPLATE->filterForm->sortingType = $CC_SYSTEM->request->params['sortingType'];
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
            
            if (isset($values['rentalOffice']) && (int)$values['rentalOffice'] > 0) {
                $params['ro_id'] = (int)$values['rentalOffice'];
            }
            if (isset($values['vehicleFuelType']) && (int)$values['vehicleFuelType'] > 0) {
                $params['vft_id'] = (int)$values['vehicleFuelType'];
            }
            if (isset($values['vehicleColor']) && (int)$values['vehicleColor'] > 0) {
                $params['vc_id'] = (int)$values['vehicleColor'];
            }
            if (isset($values['costPerDayFrom']) && !empty($values['costPerDayFrom'])) {
                $params['costPerDayFrom'] = (float)$values['costPerDayFrom'];
            }
            if (isset($values['costPerDayTo']) && !empty($values['costPerDayTo'])) {
                $params['costPerDayTo'] = (float)$values['costPerDayTo'];
            }
            if (isset($values['costPerUnitFrom']) && !empty($values['costPerUnitFrom'])) {
                $params['costPerUnitFrom'] = (float)$values['costPerUnitFrom'];
            }
            if (isset($values['costPerUnitTo']) && !empty($values['costPerUnitTo'])) {
                $params['costPerUnitTo'] = (float)$values['costPerUnitTo'];
            }
            if (isset($values['vehicleAvailability']) && $values['vehicleAvailability'] > 0) {
                $params['vehicleAvailability'] = $values['vehicleAvailability'];
            }
            if (isset($values['sorting']) && !empty($values['sorting'])) {
                $params['sorting'] = $values['sorting'];
            }
            if (isset($values['sortingType']) && !empty($values['sortingType'])) {
                $params['sortingType'] = $values['sortingType'];
            }
            
            
            // Presmerujeme na stranku katalogu, nastavime filtrovaci parametry
            CC_Utils::redirect(':Catalog:default', $params);
        }
        // Bylo stisknuto jine tlacitko (zrusi filtry)
        else {
            CC_Utils::redirect(':Catalog:default', array());
        }
    }

    
    
    // Ulozime si seznamy pro filtry
    $CC_TEMPLATE->rentalOfficeList = array();
    $CC_TEMPLATE->vehicleColorList = array();
    $CC_TEMPLATE->vehicleFuelTypeList = array();
    
    $list = $CC_SYSTEM->modelLoader->createList('CC_RentalOffice');
    foreach ($list->find() as $item) {
        $CC_TEMPLATE->rentalOfficeList[$item->getId()] = $item->getName();
    }
    
    $list = $CC_SYSTEM->modelLoader->createList('CC_VehicleColor');
    foreach ($list->find() as $item) {
        $CC_TEMPLATE->vehicleColorList[$item->getId()] = $item->getName();
    }
    
    $list = $CC_SYSTEM->modelLoader->createList('CC_VehicleFuelType');
    foreach ($list->find() as $item) {
        $CC_TEMPLATE->vehicleFuelTypeList[$item->getId()] = $item->getFuel();
    }
    
    
    // Vytvori instanci strankovace pro seznam vypujcek
    $CC_TEMPLATE->paginator = new CC_Paginator;
    $CC_TEMPLATE->paginator->setPage(isset($CC_SYSTEM->request->params['page']) ? $CC_SYSTEM->request->params['page'] : 1);
    $CC_TEMPLATE->paginator->setItemsPerPage(8);
    
    
    // Pripravime filtr a razeni
    $filter = array();
    $sorter = array('vehicle_name' => 'ASC');
    
    if ($CC_TEMPLATE->filterForm->rentalOffice) {
        $filter['rentalOffice_id'] = $CC_TEMPLATE->filterForm->rentalOffice;
    }
    if ($CC_TEMPLATE->filterForm->vehicleFuelType) {
        $filter['vehicleFuelType_id'] = $CC_TEMPLATE->filterForm->vehicleFuelType;
    }
    if ($CC_TEMPLATE->filterForm->vehicleColor) {
        $filter['vehicleColor_id'] = $CC_TEMPLATE->filterForm->vehicleColor;
    }
    if ($CC_TEMPLATE->filterForm->costPerDayFrom) {
        $filter['costPerDay_from'] = $CC_TEMPLATE->filterForm->costPerDayFrom;
    }
    if ($CC_TEMPLATE->filterForm->costPerDayTo) {
        $filter['costPerDay_to'] = $CC_TEMPLATE->filterForm->costPerDayTo;
    }
    if ($CC_TEMPLATE->filterForm->costPerUnitFrom) {
        $filter['costPerUnit_from'] = $CC_TEMPLATE->filterForm->costPerUnitFrom;
    }
    if ($CC_TEMPLATE->filterForm->costPerUnitTo) {
        $filter['costPerUnit_to'] = $CC_TEMPLATE->filterForm->costPerUnitTo;
    }
    
    if ($CC_TEMPLATE->filterForm->sorting) {
        $sorter = array($CC_TEMPLATE->filterForm->sorting => $CC_TEMPLATE->filterForm->sortingType);
    }
    
    // Ziskame vsechny vypujcky pro daneho uzivatele
    $list = $CC_SYSTEM->modelLoader->createList('CC_Vehicle');
    $vehicles = $list->find($filter, $sorter, $CC_TEMPLATE->paginator->getItemsPerPage(), $CC_TEMPLATE->paginator->getOffset());
    
    
    // Nastavi celkovy pocet vypujcek a 
    $CC_TEMPLATE->paginator->setTotalCount($list->getTotalCount());
    
    // Seznam nalezenych vozidel
    $CC_TEMPLATE->vehicles = $vehicles;
    
    
}
/** ************************************************************************* */
/** Akce = detail                                                             */ 
/** ************************************************************************* */
if ($CC_SYSTEM->request->action == 'detail') {

    $vehicle = null;
    
    // Pokud je zadano ID vozidla
    if (isset($CC_SYSTEM->request->params['id']) && !empty($CC_SYSTEM->request->params['id'])) {
        // Zkusime najit vozidlo
        $vehicle = $CC_SYSTEM->modelLoader->get('CC_Vehicle', (int)$CC_SYSTEM->request->params['id']);
        if (!$vehicle) {
            // Vozidlo nenalezeno, presmerujeme
            $CC_TEMPLATE->flashMessages['danger'][] = 'Vozidlo nebylo nalezeno!';
            CC_Utils::redirect(':Catalog:default');
        }
    } else {
        // ID neni zadano, presmerujeme
        $CC_TEMPLATE->flashMessages['danger'][] = 'Vozidlo nebylo nalezeno!';
        CC_Utils::redirect(':Catalog:default');
    }
    
    
    // Mame vozidlo
    if ($vehicle) {
        $vehicleFullName = $vehicle->getFullName() . ', ' . mb_strtolower($vehicle->getVehicleColor()->getName(), 'UTF-8');
        
        // Nastavi titulek a nadpis stranky
        $CC_TEMPLATE->pageTitle = $vehicleFullName;
        $CC_TEMPLATE->pageHeading = $vehicleFullName;

        // Prida polozku do drobeckove navigace
        $item = new stdClass;
        $item->href = ':Catalog:default';
        $item->params = array();
        $item->title = 'Katalog vozidel';
        $CC_TEMPLATE->breadcrumb[] = $item;
        
        $item = new stdClass;
        $item->href = 'this';
        $item->params = array();
        $item->title = $vehicleFullName;
        $CC_TEMPLATE->breadcrumb[] = $item;
        
        // Vychozi hodnoty pro rezervacni formular
        $CC_TEMPLATE->reservationForm = new stdClass;
        $CC_TEMPLATE->reservationForm->reservedFrom = null;
        $CC_TEMPLATE->reservationForm->reservedTo = null;
        $CC_TEMPLATE->reservationForm->rentalOffice = $vehicle->getRentalOffice()->getId();
        $CC_TEMPLATE->reservationForm->errors = array();
        
        /** ********************************************************************* */
        /** Signal = reservationForm                                              */ 
        /** ********************************************************************* */
        if ($CC_SYSTEM->request->signal == 'reservationForm') {
            // Pokud nemame data v postu nebo neni prihlaseny uzivatel
            if (!$CC_TEMPLATE->user || empty($CC_SYSTEM->request->post)) {
                CC_Utils::redirect('this', array('id' => $vehicle->getId()));
            }

            // Vytahneme si data z postu
            $values = $CC_SYSTEM->request->post;
            
            
            // Overime platnost datumu zacatku rezervace
            if (empty($values['reservedFrom']) || !preg_match('~^\d{2}\.\d{2}\.\d{4}$~', $values['reservedFrom'])) {
                $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Začátek rezervace</strong> není ve správném formátu! Datum musí být ve formátu DD.MM.RRRR.';
                $CC_TEMPLATE->reservationForm->errors['reservedFrom'] = true;
            } else {
                try {
                    $CC_TEMPLATE->reservationForm->reservedFrom = new DateTime($values['reservedFrom'] . ' 23:59:59');
                    
                    // Overime zda je zadano datum v budoucnu
                    $now = new DateTime;
                    if ($now > $CC_TEMPLATE->reservationForm->reservedFrom) {
                        throw new Exception();
                    }
                } catch(Exception $e) {
                    $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Začátek rezervace</strong> není ve správném formátu! Datum musí být ve formátu DD.MM.RRRR.';
                    $CC_TEMPLATE->reservationForm->errors['reservedFrom'] = true;
                }
            }
            // Overime platnostu datumu konce rezervace
            if (empty($values['reservedTo']) || !preg_match('~^\d{2}\.\d{2}\.\d{4}$~', $values['reservedTo'])) {
                $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Konec rezervace</strong> není ve správném formátu! Datum musí být ve formátu DD.MM.RRRR.';
                $CC_TEMPLATE->reservationForm->errors['reservedTo'] = true;
            } else {
                try {
                    $CC_TEMPLATE->reservationForm->reservedTo = new DateTime($values['reservedTo'] . ' 23:59:59');
                    
                    // Overime zda je zadano datum v budoucnu
                    $now = new DateTime;
                    if ($now > $CC_TEMPLATE->reservationForm->reservedTo) {
                        throw new Exception();
                    }
                } catch(Exception $e) {
                    $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Konec rezervace</strong> není ve správném formátu! Datum musí být ve formátu DD.MM.RRRR.';
                    $CC_TEMPLATE->reservationForm->errors['reservedTo'] = true;
                }
            }
            
            
            // Overime zda jsou datumy zadany ve spravnem poradi
            if ($CC_TEMPLATE->reservationForm->reservedFrom > $CC_TEMPLATE->reservationForm->reservedTo) {
                $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Začátek a konec rezervace</strong> nejsou zadány ve správném pořadí!';
                $CC_TEMPLATE->reservationForm->errors['reservedFrom'] = true;
                $CC_TEMPLATE->reservationForm->errors['reservedTo'] = true;
            } 
            // Overime, zda je vozidlo dostupne
            else if (false) {
                $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Vozidlo nelze rezervovat!</strong> V tuto dobu je již vozidlo rezervované.';
                $CC_TEMPLATE->reservationForm->errors['reservedFrom'] = true;
                $CC_TEMPLATE->reservationForm->errors['reservedTo'] = true;
            }
            
            
            // Overime zda je zadana platna pobocka
            $CC_TEMPLATE->reservationForm->rentalOffice = $CC_SYSTEM->modelLoader->get('CC_RentalOffice', $values['rentalOffice']);
            if (!$CC_TEMPLATE->reservationForm->rentalOffice) {
                $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Zvolená pobočka neexistuje!</strong>';
                $CC_TEMPLATE->reservationForm->errors['rentalOffice'] = true;
            }
            
            
            // Vse OK, vytvorime a ulozime novou vypujcku
            if (empty($CC_TEMPLATE->reservationForm->errors)) {
                $loan = $CC_SYSTEM->modelLoader->create('CC_Loan');
                $loan->setVehicle($vehicle)
                        ->setCustomer($CC_TEMPLATE->user)
                        ->setRentalOffice($CC_TEMPLATE->reservationForm->rentalOffice)
                        ->setReservedAt(new DateTime)
                        ->setReservedFrom($CC_TEMPLATE->reservationForm->reservedFrom)
                        ->setReservedTo($CC_TEMPLATE->reservationForm->reservedTo);
                
                // Ulozime vypujcku
                if ($loan->persist()) {
                    // Vse ok, nastavime flash zpravicku a presmerujeme
                    $CC_TEMPLATE->flashMessages['success'][] = 'Rezervace vozidla proběhla v pořádku. Výpůjčka byla úspěšně vytvořena!';
                    CC_Utils::redirect('this', array('id' => $vehicle->getId()));
                } else {
                    $CC_TEMPLATE->flashMessages['danger'][] = 'Při rezervaci vozidla došlo k chybě! Opakujte akci znovu.';
                    $CC_TEMPLATE->reservationForm->errors['user'] = true;
                }
            }
            
            
            // Pokud doslo pri zpracovani formulare k chybe!
            if (count($CC_TEMPLATE->reservationForm->errors) > 0) {
                // Predame hodnoty do formulare
                $CC_TEMPLATE->reservationForm->reservedFrom = $values['reservedFrom'];
                $CC_TEMPLATE->reservationForm->reservedTo = $values['reservedTo'];
                $CC_TEMPLATE->reservationForm->rentalOffice = $values['rentalOffice'];
            }
        }

        
        // Pridame vozidlo do sablony
        $CC_TEMPLATE->vehicle = $vehicle;
        
        // Seznam pobocek pro rezervaci vozidla
        $CC_TEMPLATE->rentalOfficeList = array();
        $list = $CC_SYSTEM->modelLoader->createList('CC_RentalOffice');
        foreach ($list->find() as $item) {
            $CC_TEMPLATE->rentalOfficeList[$item->getId()] = $item->getName();
        }
        
        // Pripravime filtr a razeni
        $filter = array();
        $sorter = array('reservedAt DESC', 'startAt DESC', 'endAt DESC');
        
        $filter['vehicle_id'] = $vehicle->getId();
        $filter['active_loans'] = true;

        // Získáme seznam výpůjček pro dané vozidlo
        $list = $CC_SYSTEM->modelLoader->createList('CC_Loan');
        $CC_TEMPLATE->loanList = $list->find($filter, $sorter);
    }
}