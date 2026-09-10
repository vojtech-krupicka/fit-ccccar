<?php
/**
 * CC_Employee.php
 *
 * Entita reprezentujici zamestnance. Odpovida tabulce 'employee'.
 *
 * @copyright 2014
 * @version 1.0.0
 */


final class CC_Employee extends CC_Entity
{

    /** @var CC_RentalOffice */
    private $rentalOffice = NULL;


    /** @var string */
    private $name = NULL;


    /** @var string */
    private $surname = NULL;


    /** @var string */
    private $phone = NULL;


    /** @var string */
    private $email = NULL;
    
    
    
    /**
     * Pokusi se najit uzivatele s danym prihlasovacim emailem a heslem v DB.
     *   
     * @param CC_Database $dbconn
     * @param string $email Prihlasovaci email
     * @param string $password Nezasifrovane heslo
     * @return int|NULL Vraci ID zakaznika pokud jsou udaje spravne, jinak NULL.
     */
    public static function authenticate(CC_Database $dbconn, $email, $password)
    {
        // Zasifrujeme heslo
        $hashPssw = strtolower(hash('sha256', $password));
        
        // Dotaz pro ziskani ID zakaznika z emailu a hesla
        $sql = "SELECT `id` FROM `employee` WHERE `email` = '$email' AND `password` = '$hashPssw'";
        $res = $dbconn->query($sql);
        
        // Test, zda byl skutecne vracen pouze 1 radek
        if ($res->numRows == 1) {
            $row = $dbconn->fetch($res->result);
            return (int)$row->id;
        } 
        
        return null;
    }
    
    
    /**
     * Overi, zda zadany email existuje v DB.
     * 
     * @param CC_Database $dbconn
     * @param type $email
     * @return boolean Vraci true, pokud email v DB jiz existuje, jinak false
     */
    public static function emailExists(CC_Database $dbconn, $email)
    {
        // Dotaz pro ziskani ID zakaznika z emailu a hesla
        $sql = "SELECT count(*) AS cnt FROM `employee` WHERE `email` = '$email'";
        $res = $dbconn->query($sql);
        
        // Test, zda byl skutecne vracen pouze 1 radek
        if ($res->numRows == 1) {
            $row = $dbconn->fetch($res->result);
            if ((int)$row->cnt == 1) {
                return true;
            }
        } 
        
        return false;
    }
    
    
    /**
     * Ulozi nove heslo uzivateli, ktere predtim zasifruje.
     * 
     * @param type $password
     * @return boolean Vraci true, pokud bylo heslo uspesne vlozeno, jinak false
     */
    public function savePassword($password)
    {
        // Test, zda je ID platne
        if ($this->id <= 0) {
            return false;
        }
        
        // Zasifrujeme heslo
        $hashPssw = strtolower(hash('sha256', $password));
        
        // Dotaz pro ziskani ID zakaznika z emailu a hesla
        $sql = "UPDATE `employee` SET `password` = '$hashPssw' WHERE `id` = '" . $this->id . "'";
        $res = $this->dbconn->query($sql);
        
        // Pokud doslo k aktualizaci
        if ($res->result && !$res->error) {
            return true;
        }
        
        return false;
    }
    


    /** ********************************************************************* */
    /** Rozhrani CC_Entity                                                    */
    /** ********************************************************************* */


    /**
     * Nacte entitu.
     * 
     * @see CC_Entity::load()
     * @return boolean
     */
    public function load($filter = array())
    {
        // Test, zda je ID platne
        if ($this->id <= 0) {
            return false;
        }
        
        // Ziskame zaznam z DB
        $sql = "SELECT `rentalOffice_id`, `name`, `surname`, `phone`, `email`"
                ." FROM `employee` WHERE `id` = '" . $this->id . "'";
        $res = $this->dbconn->query($sql);
        
        // Test, zda byl skutecne vracen pouze 1 radek
        if ($res->numRows == 1) {
            $row = $this->dbconn->fetch($res->result);
                
            // Naplni instanci daty z DB
            $this->name = $row->name;
            $this->surname = $row->surname;
            $this->phone = $row->phone;
            $this->email = $row->email;
            
            $this->rentalOffice = ($row->rentalOffice_id) ? $this->loader->get('CC_RentalOffice', $row->rentalOffice_id) : null;
            
            
            
            return true;
        }
        
        return false;
    }


    /**
     * Vytvori novou entitu v DB.
     * 
     * @see CC_Entity::create()
     * @return boolean
     */
    protected function create()
    {
        // Vlozime novy zaznam do DB
        $sql = "INSERT INTO `employee` (...) VALUES (...)";
        $res = $this->dbconn->query($sql);
        
        // Test, zda bylo neco vlozeno
        if ($res->affectedRows == 1) {
            // Nastavime nove ID uzivateli
            $this->id = (int)$res->lastId;
            return true;
        }
        
        return false;
    }


    /**
     * Aktualizuje stavajici entitu v DB.
     * 
     * @see CC_Entity::update()
     * @return boolean
     */
    protected function update()
    {
        // Test, zda je ID platne
        if ($this->id <= 0) {
            return false;
        }
        
        // Vlozime novy zaznam do DB
        $sql = "UPDATE `employee` SET "
                ."`name` = '".$this->name."', `surname` = '".$this->surname."', "
                ."`phone` = '".$this->phone."', `email` = '".$this->email."'"
                ." WHERE `id` = '".$this->id."'";
	    $res = $this->dbconn->query($sql);
        
        // Test, zda bylo neco vlozeno
        if ($res->result && !$res->error) {
            return true;
        }
        
        return false;
    }


    /**
     * Smaze entitu z DB.
     * 
     * @see CC_Entity::delete()
     * @return boolean
     */
    public function delete()
    {
        // Test, zda je ID platne
        if ($this->id <= 0) {
            return false;
        }
        
        // Vlozime novy zaznam do DB
        $sql = "DELETE FROM `employee` WHERE `id` = '".$this->id."'";
        $res = $this->dbconn->query($sql);
        
        // Test, zda bylo neco vlozeno
        if ($res->result && !$res->error) {
            // Entita smazana, nyni je nutne ji odebrat z modelLoaderu!
            $this->loader->detach($this);
            $this->id = null;
            return true;
        }
       
       return false;
    }


    /** ********************************************************************* */
    /** Gettery                                                               */
    /** ********************************************************************* */


    /**
     * Vraci instanci pobocky.
     *
     * @return int
     */
    public function getRentalOffice()
    {
        return $this->rentalOffice;
    }


    /**
     * Vraci krestni jmeno zamestnance.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Vraci prijmeni zamestnance.
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }
    
    
    /**
     * Vraci cele jmeno zamestnance.
     *
     * @return string
     */
    public function GetFullName()
    {
        return $this->name . ' ' . $this->surname;
    }


    /**
     * Vraci telefon.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }


    /**
     * Vraci email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }


    /** ********************************************************************* */
    /** Settery                                                               */
    /** ********************************************************************* */


    /**
     * Nastavi pobocku pro zamestnance.
     *
     * @param CC_RentalOffice $rentalOffice
     * @return CC_Employee
     */
    public function setRentalOfficeId(CC_RentalOffice $rentalOfficeId)
    {
        $this->rentalOffice = $rentalOffice;
        return $this;
    }


    /**
     * Nastavi krestni jmeno zamestnance.
     *
     * @param string $name
     * @return CC_Employee
     */
    public function setName($name)
    {
        $this->name = (string)$name;
        return $this;
    }


    /**
     * Nastavi prijmeni.
     *
     * @param string $surname
     * @return CC_Employee
     */
    public function setSurname($surname)
    {
        $this->surname = (string)$surname;
        return $this;
    }


    /**
     * Nastavi telefon.
     *
     * @param string $phone
     * @return CC_Employee
     */
    public function setPhone($phone)
    {
        $this->phone = (string)$phone;
        return $this;
    }


    /**
     * Nastavi email.
     *
     * @param string $email
     * @return CC_Employee
     */
    public function setEmail($email)
    {
        $this->email = (string)$email;
        return $this;
    }


}
