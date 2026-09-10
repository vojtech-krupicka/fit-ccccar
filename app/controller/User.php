<?php
/**
 * User.php
 *
 * User kontroler - zobrazuje informace o preihlasenem uzivateli - zakaznikovi.
 * Umoznuje prihlaseni, odhlaseni, registraci, editaci profilu a zobrazuje
 * seznam vsech jeho vypujcek s moznosti filtrace, razeni a strankovani.
 *
 * @copyright 2014
 * @version 1.0.0
 */


/** ************************************************************************* */
/** Akce = login                                                              */ 
/** ************************************************************************* */
if ($CC_SYSTEM->request->action == 'login') {
    // Pokud je jiz uzivatel prihlaseny, nepovolime mu sem pristup
    if ($CC_SYSTEM->user) {
        CC_Utils::redirect(':Homepage:default');
    }
    
    
    // Nastavi titulek a nadpis stranky
    $CC_TEMPLATE->pageTitle = 'Přihlášení';
    $CC_TEMPLATE->pageHeading = 'Přihlášení';
    
    // Prida polozku do drobeckove navigace
    $item = new stdClass;
    $item->href = 'this';
    $item->params = array();
    $item->title = 'Přihlášení';
    $CC_TEMPLATE->breadcrumb[] = $item;
    
    // Vychozi hodnoty pro formular
    $CC_TEMPLATE->loginForm = new stdClass;
    $CC_TEMPLATE->loginForm->email = '';
    $CC_TEMPLATE->loginForm->password = '';
    $CC_TEMPLATE->loginForm->persistent = false;
    $CC_TEMPLATE->loginForm->errors = array();
    
    
    
    /** ********************************************************************* */
    /** Signal = loginForm                                                    */ 
    /** ********************************************************************* */
    if ($CC_SYSTEM->request->signal == 'loginForm') {
        // Pokud nemame data v postu
        if (empty($CC_SYSTEM->request->post)) {
            CC_Utils::redirect('this');
        }
        
        // Vytahneme si data z postu
        $values = $CC_SYSTEM->request->post;
                
        // Kontrola, zda jsou zadane udaje spravne
        if (empty($values['email'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = 'Není vyplněn <strong>přihlašovací email</strong>!';
            $CC_TEMPLATE->loginForm->errors['email'] = true;
        }
        if (empty($values['password'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = 'Není zadáno <strong>přihlašovací heslo</strong>!';
            $CC_TEMPLATE->loginForm->errors['password'] = true;
        }
        
        // Pokusime se nacist uzivatele z DB podle zadaneho uzivatelskeho emailu a hesla
        if (empty($CC_TEMPLATE->loginForm->errors)) {
            $userId = CC_Customer::authenticate($CC_SYSTEM->dbconn, $values['email'], $values['password']);
            if ($userId === null) {
                $CC_TEMPLATE->flashMessages['danger'][] = 'Nesprávné přihlašovací jméno nebo heslo!';
                $CC_TEMPLATE->loginForm->errors['user'] = true;
            }
        }
        
        // Pokud doslo pri zpracovani formulare k chybe!
        if (count($CC_TEMPLATE->loginForm->errors) > 0) {
            // Predame hodnoty do formulare
            $CC_TEMPLATE->loginForm->email = $values['email'];
            $CC_TEMPLATE->loginForm->persistent = isset($values['persistent']) ? true : false;
        } else {
            // Zadna chyba - nastavime session pro prihlaseneho uzivatele a presmerujeme
            CC_Utils::sessionSet('employee-id', null);
            CC_Utils::sessionSet('customer-id', $userId);
            CC_Utils::sessionSet('customer-persistent', isset($values['persistent']) ? true : false);
            
            // Nastavime flash zpravicku
            $CC_TEMPLATE->flashMessages['success'][] = 'Byl jste úspěšně přihlášen.';
            
            // Presmerujeme na hlavni stranku - uzivatel bude prihlasen
            CC_Utils::redirect(':Homepage:default');
        }
    }
}
/** ************************************************************************* */
/** Akce = logout                                                             */ 
/** ************************************************************************* */
else if ($CC_SYSTEM->request->action == 'logout') {
    // Pokud neni uzivatel prihlaseny, nepovolime mu sem pristup
    if (!$CC_SYSTEM->user) {
        CC_Utils::redirect(':Homepage:default');
    }
    
    // Zrusime uzivatelskou session
    CC_Utils::sessionSet('customer-id', null);
    
    // Nastavime flash zpravicku
    $CC_TEMPLATE->flashMessages['success'][] = 'Byl jste úspěšně odhlášen ze systému.';
    
    // Presmeruje na hlavni stranku
    CC_Utils::redirect(':Homepage:default');
}
/** ************************************************************************* */
/** Akce = registration                                                       */ 
/** ************************************************************************* */
else if ($CC_SYSTEM->request->action == 'registration') {
    // Pokud je jiz uzivatel prihlaseny, nepovolime mu sem pristup
    if ($CC_SYSTEM->user) {
        CC_Utils::redirect(':Homepage:default');
    }
    
    // Nastavi titulek a nadpis stranky
    $CC_TEMPLATE->pageTitle = 'Registrace';
    $CC_TEMPLATE->pageHeading = 'Registrace';
    
    // Prida polozku do drobeckove navigace
    $item = new stdClass;
    $item->href = 'this';
    $item->params = array();
    $item->title = 'Registrace';
    $CC_TEMPLATE->breadcrumb[] = $item;
    
    // Vychozi hodnoty pro formular
    $CC_TEMPLATE->regForm = new stdClass;
    $CC_TEMPLATE->regForm->email = '';
    $CC_TEMPLATE->regForm->password = '';
    $CC_TEMPLATE->regForm->passwordCheck = '';
    $CC_TEMPLATE->regForm->name = '';
    $CC_TEMPLATE->regForm->surname = '';
    $CC_TEMPLATE->regForm->idNumber = '';
    $CC_TEMPLATE->regForm->phoneNumber = '';
    $CC_TEMPLATE->regForm->street = '';
    $CC_TEMPLATE->regForm->city = '';
    $CC_TEMPLATE->regForm->country = 0;
    $CC_TEMPLATE->regForm->zip = '';
    $CC_TEMPLATE->regForm->agreed = false;
    $CC_TEMPLATE->regForm->errors = array();
    
    
    /** ********************************************************************* */
    /** Signal = regForm                                                      */ 
    /** ********************************************************************* */
    if ($CC_SYSTEM->request->signal == 'regForm') {
        // Pokud nemame data v postu
        if (empty($CC_SYSTEM->request->post)) {
            CC_Utils::redirect('this');
        }
        
        // Validator pro zemi puvodu
        $country = array(
            'cr' => 'Česká Republika',
            'sr' => 'Slovensko',
        );
        
        // Vytahneme si data z postu
        $values = $CC_SYSTEM->request->post;
        //dump($values);
        
        // Overime, zda registracni email jiz neni v DB
        if (CC_Customer::emailExists($CC_SYSTEM->dbconn, $values['email'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Email</strong> je již registrován! Můžete se <a href="'.CC_Utils::link(':User:login').'" title="Přihlášení">přihlásit</a>.';
            $CC_TEMPLATE->regForm->errors['email'] = true;
        }
        
        // Kontrola, zda jsou zadane udaje spravne
        if (empty($values['email']) || !preg_match('~^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$~', $values['email'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Registrační email</strong> není ve správném formátu!';
            $CC_TEMPLATE->regForm->errors['email'] = true;
        }
        if (strlen($values['password']) < 6 || strlen($values['password']) > 64) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Přihlašovací heslo</strong> není ve správném formátu! Heslo musí být 6-64 znaků dlouhé.';
            $CC_TEMPLATE->regForm->errors['password'] = true;
        }
        if ($values['password'] !== $values['passwordCheck']) {
            $CC_TEMPLATE->flashMessages['danger'][] = 'Přihlašovací heslo a heslo pro kontrolu se neshodují!';
            $CC_TEMPLATE->regForm->errors['passwordCheck'] = true;
        }
        if (empty($values['name']) || strlen($values['name']) > 64) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Jméno</strong> musí být zadáno a musí být max. 64 znaků dlouhé!';
            $CC_TEMPLATE->regForm->errors['name'] = true;
        }
        if (empty($values['surname']) || strlen($values['surname']) > 64) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Příjmení</strong> musí být zadáno a musí být max. 64 znaků dlouhé!';
            $CC_TEMPLATE->regForm->errors['surname'] = true;
        }
        if (empty($values['idNumber']) || strlen($values['idNumber']) != 9 || !is_int((int)$values['idNumber'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Číslo OP</strong> musí být zadáno a musí obsahovat právě 9 cifer!';
            $CC_TEMPLATE->regForm->errors['idNumber'] = true;
        }
        if (empty($values['phoneNumber']) || strlen($values['phoneNumber']) != 9 || !is_int((int)$values['phoneNumber'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Telefon</strong> musí být zadán a musí obsahovat právě 9 cifer!';
            $CC_TEMPLATE->regForm->errors['phoneNumber'] = true;
        }
        if (empty($values['street']) || strlen($values['street']) > 64) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Jméno ulice</strong> musí být zadána a musí být max. 64 znaků dlouhé!';
            $CC_TEMPLATE->regForm->errors['street'] = true;
        }
        if (empty($values['city']) || strlen($values['city']) > 64) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Město</strong> musí být zadána a musí být max. 64 znaků dlouhý!';
            $CC_TEMPLATE->regForm->errors['city'] = true;
        }
        if (!in_array($values['country'], array_keys($country))) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Stát původu</strong> musí být vybrán!';
            $CC_TEMPLATE->regForm->errors['country'] = true;
        }
        if (empty($values['zip']) || strlen($values['zip']) != 5 || !is_int((int)$values['zip'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>PSČ</strong> musí být zadáno a musí obsahovat právě 5 cifer!';
            $CC_TEMPLATE->regForm->errors['zip'] = true;
        }
        if (!isset($values['agreed'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = 'Před registrací musíte souhlasit s <strong>obchodními podmínkami</strong>!';
            $CC_TEMPLATE->regForm->errors['agreed'] = true;
        }
        
        
        // Vytvorime noveho uzivatele a nastavime mu hodnoty
        if (empty($CC_TEMPLATE->regForm->errors)) {
            $user = $CC_SYSTEM->modelLoader->create('CC_Customer');
            $user->setEmail($values['email'])
                ->setName($values['name'])
                ->setSurname($values['surname'])
                ->setIdentityNumber($values['idNumber'])
                ->setPhone($values['phoneNumber'])
                ->setStreet($values['street'])
                ->setCity($values['city'])
                ->setCountry($country[$values['country']])
                ->setZip($values['zip']);
            
            // Ulozime uzivatele a nastavime mu heslo
            if ($user->persist() && $user->savePassword($values['password'])) {
                // Vse ok, nastavime flash zpravicku a presmerujeme
                $CC_TEMPLATE->flashMessages['success'][] = 'Registrace proběhla v pořádku, nyní se můžete přihlásit.';
                CC_Utils::redirect(':User:login');
            } else {
                $CC_TEMPLATE->flashMessages['danger'][] = 'Při registraci došlo k chybě! Opakujte akci znovu.';
                $CC_TEMPLATE->regForm->errors['user'] = true;
            }
        }
        
        // Pokud doslo pri zpracovani formulare k chybe!
        if (count($CC_TEMPLATE->regForm->errors) > 0) {
            // Predame hodnoty do formulare
            $CC_TEMPLATE->regForm->email = $values['email'];
            $CC_TEMPLATE->regForm->name = $values['name'];
            $CC_TEMPLATE->regForm->surname = $values['surname'];
            $CC_TEMPLATE->regForm->idNumber = $values['idNumber'];
            $CC_TEMPLATE->regForm->phoneNumber = $values['phoneNumber'];
            $CC_TEMPLATE->regForm->street = $values['street'];
            $CC_TEMPLATE->regForm->city = $values['city'];
            $CC_TEMPLATE->regForm->country = $values['country'];
            $CC_TEMPLATE->regForm->zip = $values['zip'];
        }
    }
}
/** ************************************************************************* */
/** Akce = profile                                                            */ 
/** ************************************************************************* */
else if ($CC_SYSTEM->request->action == 'profile') {
    // Pokud neni uzivatel prihlaseny, nepovolime mu sem pristup
    if (!$CC_SYSTEM->user) {
        CC_Utils::redirect(':Homepage:default');
    }
    
    // Nastavi titulek a nadpis stranky
    $CC_TEMPLATE->pageTitle = 'Můj profil';
    $CC_TEMPLATE->pageHeading = 'Můj profil';
    
    // Prida polozku do drobeckove navigace
    $item = new stdClass;
    $item->href = 'this';
    $item->params = array();
    $item->title = 'Můj profil';
    $CC_TEMPLATE->breadcrumb[] = $item;
    
    // Vychozi hodnoty pro formular
    $CC_TEMPLATE->editForm = new stdClass;
    $CC_TEMPLATE->editForm->email = $CC_SYSTEM->user->getEmail();
    $CC_TEMPLATE->editForm->passwordOld = '';
    $CC_TEMPLATE->editForm->passwordNew = '';
    $CC_TEMPLATE->editForm->passwordCheck = '';
    $CC_TEMPLATE->editForm->name = $CC_SYSTEM->user->getName();
    $CC_TEMPLATE->editForm->surname = $CC_SYSTEM->user->getSurname();
    $CC_TEMPLATE->editForm->idNumber = $CC_SYSTEM->user->getIdentityNumber();
    $CC_TEMPLATE->editForm->phoneNumber = $CC_SYSTEM->user->getPhone();
    $CC_TEMPLATE->editForm->street = $CC_SYSTEM->user->getStreet();
    $CC_TEMPLATE->editForm->city = $CC_SYSTEM->user->getCity();
    $CC_TEMPLATE->editForm->country = ($CC_SYSTEM->user->getCountry() == 'Česká Republika') ? 'cr' : 'sr';
    $CC_TEMPLATE->editForm->zip = $CC_SYSTEM->user->getZip();
    $CC_TEMPLATE->editForm->errors = array();
    
    
    /** ********************************************************************* */
    /** Signal = editForm                                                      */ 
    /** ********************************************************************* */
    if ($CC_SYSTEM->request->signal == 'editForm') {
        // Pokud nemame data v postu
        if (empty($CC_SYSTEM->request->post)) {
            CC_Utils::redirect('this');
        }
        
        // Validator pro zemi puvodu
        $country = array(
            'cr' => 'Česká Republika',
            'sr' => 'Slovensko',
        );
        
        // Vytahneme si data z postu
        $values = $CC_SYSTEM->request->post;
        //dump($values);
        
        // Pokud bylo vyplneno stare heslo, bude se zpravovat jeho zmena
        if (!empty($values['passwordOld'])) {
            // Nejprve otestujeme, zda je stare heslo zadano spravne
            $userId = CC_Customer::authenticate($CC_SYSTEM->dbconn, $CC_SYSTEM->user->getEmail(), $values['passwordOld']);
            if (!$userId || $userId != $CC_SYSTEM->user->getId()) {
                $CC_TEMPLATE->flashMessages['danger'][] = 'Vaše <strong>staré heslo</strong> není správné!';
                $CC_TEMPLATE->editForm->errors['passwordOld'] = true;
            }
            
            // Nyni overime nove heslo a heslo pro kontrolu
            if (strlen($values['passwordNew']) < 6 || strlen($values['passwordNew']) > 64) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Nové heslo</strong> není ve správném formátu! Heslo musí být 6-64 znaků dlouhé.';
            $CC_TEMPLATE->editForm->errors['passwordNew'] = true;
            }
            if ($values['passwordNew'] !== $values['passwordCheck']) {
                $CC_TEMPLATE->flashMessages['danger'][] = 'Nové přihlašovací heslo a heslo pro kontrolu se neshodují!';
                $CC_TEMPLATE->editForm->errors['passwordCheck'] = true;
            }
        }
        
        // Pokud se email lisi od soucasnyho, overime zda novy email neni jiz v DB
        if ($CC_SYSTEM->user->getEmail() != $values['email'] && CC_Customer::emailExists($CC_SYSTEM->dbconn, $values['email'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Email</strong> je již registrován! Vyberte si prosím jiný email.';
            $CC_TEMPLATE->editForm->errors['email'] = true;
        }
        
        // Kontrola, zda jsou zadane udaje spravne
        if (empty($values['email']) || !preg_match('~^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$~', $values['email'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Registrační email</strong> není ve správném formátu!';
            $CC_TEMPLATE->editForm->errors['email'] = true;
        }
        if (empty($values['name']) || strlen($values['name']) > 64) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Jméno</strong> musí být zadáno a musí být max. 64 znaků dlouhé!';
            $CC_TEMPLATE->editForm->errors['name'] = true;
        }
        if (empty($values['surname']) || strlen($values['surname']) > 64) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Příjmení</strong> musí být zadáno a musí být max. 64 znaků dlouhé!';
            $CC_TEMPLATE->editForm->errors['surname'] = true;
        }
        if (empty($values['idNumber']) || strlen($values['idNumber']) != 9 || !is_int((int)$values['idNumber'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Číslo OP</strong> musí být zadáno a musí obsahovat právě 9 cifer!';
            $CC_TEMPLATE->editForm->errors['idNumber'] = true;
        }
        if (empty($values['phoneNumber']) || strlen($values['phoneNumber']) != 9 || !is_int((int)$values['phoneNumber'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Telefon</strong> musí být zadán a musí obsahovat právě 9 cifer!';
            $CC_TEMPLATE->editForm->errors['phoneNumber'] = true;
        }
        if (empty($values['street']) || strlen($values['street']) > 64) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Jméno ulice</strong> musí být zadána a musí být max. 64 znaků dlouhé!';
            $CC_TEMPLATE->editForm->errors['street'] = true;
        }
        if (empty($values['city']) || strlen($values['city']) > 64) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Město</strong> musí být zadána a musí být max. 64 znaků dlouhý!';
            $CC_TEMPLATE->editForm->errors['city'] = true;
        }
        if (!in_array($values['country'], array_keys($country))) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Stát původu</strong> musí být vybrán!';
            $CC_TEMPLATE->editForm->errors['country'] = true;
        }
        if (empty($values['zip']) || strlen($values['zip']) != 5 || !is_int((int)$values['zip'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>PSČ</strong> musí být zadáno a musí obsahovat právě 5 cifer!';
            $CC_TEMPLATE->editForm->errors['zip'] = true;
        }
        
        
        // Vytvorime noveho uzivatele a nastavime mu hodnoty
        if (empty($CC_TEMPLATE->editForm->errors)) {
            $CC_SYSTEM->user->setEmail($values['email'])
                ->setName($values['name'])
                ->setSurname($values['surname'])
                ->setIdentityNumber($values['idNumber'])
                ->setPhone($values['phoneNumber'])
                ->setStreet($values['street'])
                ->setCity($values['city'])
                ->setCountry($country[$values['country']])
                ->setZip($values['zip']);
            
            // Ulozime uzivatele a nastavime mu heslo
            if ($CC_SYSTEM->user->persist()) {
                // Pokud bylo zadano stare heslo, zmenime ho na nove
                if ($values['passwordOld']) {
                    $CC_SYSTEM->user->savePassword($values['passwordNew']);
                }
                
                // Vse ok, nastavime flash zpravicku a presmerujeme
                $CC_TEMPLATE->flashMessages['success'][] = 'Váš profil byl úspěšně aktualizován.';
                CC_Utils::redirect('this');
            } else {
                $CC_TEMPLATE->flashMessages['danger'][] = 'Při aktualizaci vašeho profilu došlo k chybě! Opakujte akci znovu.';
                $CC_TEMPLATE->editForm->errors['user'] = true;
            }
        }
        
        // Pokud doslo pri zpracovani formulare k chybe!
        if (count($CC_TEMPLATE->editForm->errors) > 0) {
            // Predame hodnoty do formulare
            $CC_TEMPLATE->editForm->email = $values['email'];
            $CC_TEMPLATE->editForm->name = $values['name'];
            $CC_TEMPLATE->editForm->surname = $values['surname'];
            $CC_TEMPLATE->editForm->idNumber = $values['idNumber'];
            $CC_TEMPLATE->editForm->phoneNumber = $values['phoneNumber'];
            $CC_TEMPLATE->editForm->street = $values['street'];
            $CC_TEMPLATE->editForm->city = $values['city'];
            $CC_TEMPLATE->editForm->country = $values['country'];
            $CC_TEMPLATE->editForm->zip = $values['zip'];
        }
    }
}
/** ************************************************************************* */
/** Akce = loans                                                              */ 
/** ************************************************************************* */
else if ($CC_SYSTEM->request->action == 'loans') {
    // Pokud neni uzivatel prihlaseny, nepovolime mu sem pristup
    if (!$CC_SYSTEM->user) {
        CC_Utils::redirect(':Homepage:default');
    }
    
    /** ********************************************************************* */
    /** Signal = editForm                                                      */ 
    /** ********************************************************************* */
    if ($CC_SYSTEM->request->signal == 'storno') {
        // Pokud nemame ID v URL
        if (!isset($CC_SYSTEM->request->params['id']) || empty($CC_SYSTEM->request->params['id'])) {
            CC_Utils::redirect('this', $CC_SYSTEM->request->params);
        }
        
        // ID mame, najdeme vypujcku a z parametru odebereme ID
        $loan = $CC_SYSTEM->modelLoader->get('CC_Loan', $CC_SYSTEM->request->params['id']);
        unset($CC_SYSTEM->request->params['id']);
                
        if (!$loan || $loan->getCustomer()->getId() != $CC_SYSTEM->user->getId()) {
            // Pokud neexistuje nebo se ID vlastnika vypujcky neshoduje s ID prihlaseneho zakaznika
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
            CC_Utils::redirect('this', $CC_SYSTEM->request->params);
        }
    }
    
    
    
    // Nastavi titulek a nadpis stranky
    $CC_TEMPLATE->pageTitle = 'Moje výpůjčky';
    $CC_TEMPLATE->pageHeading = 'Moje výpůjčky';
    
    // Prida polozku do drobeckove navigace
    $item = new stdClass;
    $item->href = ':User:profile';
    $item->params = array();
    $item->title = 'Můj profil';
    $CC_TEMPLATE->breadcrumb[] = $item;
    
    $item = new stdClass;
    $item->href = 'this';
    $item->params = array();
    $item->title = 'Moje výpůjčky';
    $CC_TEMPLATE->breadcrumb[] = $item;

    
    // Vytvori instanci strankovace pro seznam vypujcek
    $CC_TEMPLATE->paginator = new CC_Paginator;
    $CC_TEMPLATE->paginator->setPage(isset($CC_SYSTEM->request->params['page']) ? $CC_SYSTEM->request->params['page'] : 1);
    $CC_TEMPLATE->paginator->setItemsPerPage(5);
    
    // Pripravime filtr a razeni
    $filter = array('customer_id' => $CC_SYSTEM->user->getId());
    $sorter = array('reservedAt DESC', 'startAt DESC', 'endAt DESC');
    
    // Ziskame vsechny vypujcky pro daneho uzivatele
    $list = $CC_SYSTEM->modelLoader->createList('CC_Loan');
    $loans = $list->find($filter, $sorter, $CC_TEMPLATE->paginator->getItemsPerPage(), $CC_TEMPLATE->paginator->getOffset());
    
    // Nastavi celkovy pocet vypujcek a 
    $CC_TEMPLATE->paginator->setTotalCount($list->getTotalCount());
    
    // Prida vypujcky do sablony
    $CC_TEMPLATE->loans = $loans;
}