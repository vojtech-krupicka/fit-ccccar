<?php
/**
 * AdminModule/User.php
 *
 * AdminModule/User kontroler - sprava zamestnance vcetne prihlaseni.
 * a odhlaseni.
 *
 * @copyright 2014
 * @version 1.0.0
 */


/** ************************************************************************* */
/** Akce = login                                                              */ 
/** ************************************************************************* */
if ($CC_SYSTEM->request->action == 'login') {
    // Pokud je jiz zamestnanec prihlaseny, nepovolime mu sem pristup
    if ($CC_SYSTEM->admin) {
        CC_Utils::redirect(':Admin:Homepage:default');
    }
    
    
    // Vychozi hodnoty pro formular
    $CC_TEMPLATE->loginForm = new stdClass;
    $CC_TEMPLATE->loginForm->email = '';
    $CC_TEMPLATE->loginForm->password = '';
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
            $userId = CC_Employee::authenticate($CC_SYSTEM->dbconn, $values['email'], $values['password']);
            if ($userId === null) {
                $CC_TEMPLATE->flashMessages['danger'][] = 'Nesprávné přihlašovací jméno nebo heslo!';
                $CC_TEMPLATE->loginForm->errors['user'] = true;
            }
        }
        
        // Pokud doslo pri zpracovani formulare k chybe!
        if (count($CC_TEMPLATE->loginForm->errors) > 0) {
            // Predame hodnoty do formulare
            $CC_TEMPLATE->loginForm->email = $values['email'];
        } else {
            // Zadna chyba - nastavime session pro prihlaseneho zamestnance a zrusime session pro zakaznika
            CC_Utils::sessionSet('employee-id', $userId);
            CC_Utils::sessionSet('customer-id', null);
            
            // Nastavime flash zpravicku
            $CC_TEMPLATE->flashMessages['success'][] = 'Byl jste úspěšně přihlášen do administračního systému.';
            
            // Presmerujeme na hlavni stranku - uzivatel bude prihlasen
            CC_Utils::redirect(':Admin:Homepage:default');
        }
    }
    
    // Pokud je uzivatel prihlaseny jako zakaznik, zobrazime varovani
    if ($CC_SYSTEM->user) {
        $CC_TEMPLATE->flashMessages['warning'][] = "Již jste přihlášený jako zákazník <strong>".$CC_SYSTEM->user->getFullName()."</strong>. Chcete se přihlásit jako zaměstnanec?";
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
}
/** ************************************************************************* */
/** Akce = logout                                                             */ 
/** ************************************************************************* */
else if ($CC_SYSTEM->request->action == 'logout') {
    // Pokud neni uzivatel prihlaseny, nepovolime mu sem pristup
    if (!$CC_SYSTEM->admin) {
        CC_Utils::redirect(':Admin:Homepage:default');
    }
    
    // Zrusime uzivatelskou session
    CC_Utils::sessionSet('employee-id', null);
    
    // Nastavime flash zpravicku
    $CC_TEMPLATE->flashMessages['success'][] = 'Byl jste úspěšně odhlášen z administračního systému.';
    
    // Presmeruje na hlavni stranku
    CC_Utils::redirect(':Admin:Homepage:default');
}
/** ************************************************************************* */
/** Akce = profile                                                            */ 
/** ************************************************************************* */
else if ($CC_SYSTEM->request->action == 'profile') {
    // Pokud neni uzivatel prihlaseny, nepovolime mu sem pristup
    if (!$CC_SYSTEM->admin) {
        CC_Utils::redirect(':Admin:Homepage:default');
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
    $CC_TEMPLATE->editForm->email = $CC_SYSTEM->admin->getEmail();
    $CC_TEMPLATE->editForm->passwordOld = '';
    $CC_TEMPLATE->editForm->passwordNew = '';
    $CC_TEMPLATE->editForm->passwordCheck = '';
    $CC_TEMPLATE->editForm->name = $CC_SYSTEM->admin->getName();
    $CC_TEMPLATE->editForm->surname = $CC_SYSTEM->admin->getSurname();
    $CC_TEMPLATE->editForm->phoneNumber = $CC_SYSTEM->admin->getPhone();
    $CC_TEMPLATE->editForm->errors = array();
    
    
    /** ********************************************************************* */
    /** Signal = editForm                                                      */ 
    /** ********************************************************************* */
    if ($CC_SYSTEM->request->signal == 'editForm') {
        // Pokud nemame data v postu
        if (empty($CC_SYSTEM->request->post)) {
            CC_Utils::redirect('this');
        }
        
        // Vytahneme si data z postu
        $values = $CC_SYSTEM->request->post;
        //dump($values);
        
        // Pokud bylo vyplneno stare heslo, bude se zpravovat jeho zmena
        if (!empty($values['passwordOld'])) {
            // Nejprve otestujeme, zda je stare heslo zadano spravne
            $userId = CC_Employee::authenticate($CC_SYSTEM->dbconn, $CC_SYSTEM->admin->getEmail(), $values['passwordOld']);
            if (!$userId || $userId != $CC_SYSTEM->admin->getId()) {
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
        if ($CC_SYSTEM->admin->getEmail() != $values['email'] && CC_Employee::emailExists($CC_SYSTEM->dbconn, $values['email'])) {
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
        if (empty($values['phoneNumber']) || strlen($values['phoneNumber']) != 9 || !is_int((int)$values['phoneNumber'])) {
            $CC_TEMPLATE->flashMessages['danger'][] = '<strong>Telefon</strong> musí být zadán a musí obsahovat právě 9 cifer!';
            $CC_TEMPLATE->editForm->errors['phoneNumber'] = true;
        }
        
        
        // Vytvorime noveho uzivatele a nastavime mu hodnoty
        if (empty($CC_TEMPLATE->editForm->errors)) {
            $CC_SYSTEM->admin->setEmail($values['email'])
                ->setName($values['name'])
                ->setSurname($values['surname'])
                ->setPhone($values['phoneNumber']);
            
            // Ulozime uzivatele a nastavime mu heslo
            if ($CC_SYSTEM->admin->persist()) {
                // Pokud bylo zadano stare heslo, zmenime ho na nove
                if ($values['passwordOld']) {
                    $CC_SYSTEM->admin->savePassword($values['passwordNew']);
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
            $CC_TEMPLATE->editForm->phoneNumber = $values['phoneNumber'];
        }
    }
}

