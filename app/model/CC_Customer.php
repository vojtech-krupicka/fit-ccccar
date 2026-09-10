<?php
/**
 * CC_Customer.php
 *
 * Entita reprezentujici zakaznika v systemu. Odpovida tabulce 'customer'.
 *
 * @copyright 2014
 * @version 1.0.0
 */


final class CC_Customer extends CC_Entity
{
    /** @var string */
    private $name = null;
    
    
    /** @var string */
    private $surname = null;
    
    
    /** @var string */
    private $email = null;
    
    
    /** @var int */
    private $identityNumber = null;
    
    
    /** @var string */
    private $phone = null;
    
    
    /** @var string */
    private $street = null;
    
    
    /** @var string */
    private $city = null;
    
    
    /** @var string */
    private $country = null;
    
    
    /** @var int */
    private $zip = null;
    
    
    /** @var DateTime */
    private $createdAt = null;
    
    
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
        $sql = "SELECT `id` FROM `customer` WHERE `email` = '$email' AND `password` = '$hashPssw'";
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
        $sql = "SELECT count(*) AS cnt FROM `customer` WHERE `email` = '$email'";
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
        $sql = "UPDATE `customer` SET `password` = '$hashPssw' WHERE `id` = '" . $this->id . "'";
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
     * Pokud je entita inicializovana (ma definovano ID), nacte a nastavi
     * vsechny jeji data z DB.
     * 
     * @return boolean
     */
    public function load($filter = array())
    {
        // Test, zda je ID platne
        if ($this->id <= 0) {
            return false;
        }
        
        // Ziskame zaznam z DB
        $sql = "SELECT `name`, `surname`, `identityNumber`, `email`, `phone`,"
                ." `street`, `city`, `country`, `zip`, `createdAt`"
                ." FROM `customer` WHERE `id` = '" . $this->id . "'";
        $res = $this->dbconn->query($sql);
        
        // Test, zda byl skutecne vracen pouze 1 radek
        if ($res->numRows == 1) {
            $row = $this->dbconn->fetch($res->result);
            
            // Naplni instanci daty z DB
            $this->name = $row->name;
            $this->surname = $row->surname;
            $this->email = $row->email;
            $this->identityNumber = (int)$row->identityNumber;
            $this->phone = $row->phone;
            $this->street = $row->street;
            $this->city = $row->city;
            $this->country = $row->country;
            $this->zip = (int)$row->zip;
            $this->createdAt = new DateTime($row->createdAt);
            
            return true;
        }
        
        return false;
    }
    
    
    /**
     * Vytvori novou entitu v DB.
     * 
     * @return boolean Vraci true, pokud byla entita uspesne vytvorena v DB, jinak false.
     */
    protected function create()
    {
        // Vlozime novy zaznam do DB
        $sql = "INSERT INTO `customer` "
                ."(`name`, `surname`, `identityNumber`, `email`, `phone`,"
                ." `street`, `city`, `country`, `zip`, `createdAt`) VALUES ("
                ." '" . $this->name . "', '" . $this->surname . "', '" . $this->identityNumber . "'"
                .", '" . $this->email . "', '" . $this->phone . "', '" . $this->street . "'"
                .", '" . $this->city . "', '" . $this->country . "', '" . $this->zip . "'"
                .", now())";
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
     * @return boolean Vraci true, pokud byla entita uspesne aktualizovana v DB, jinak false.
     */
    protected function update()
    {
        // Test, zda je ID platne
        if ($this->id <= 0) {
            return false;
        }
        
        // Vlozime novy zaznam do DB
        $sql = "UPDATE `customer` SET"
                ."`name` = '".$this->name."', `surname` = '".$this->surname."', "
                ."`identityNumber` = '".$this->identityNumber."', `email` = '".$this->email."', "
                ."`phone` = '".$this->phoneNumber."', `street` = '".$this->street."', "
                ."`city` = '".$this->city."', `country` = '".$this->country."', `zip` = '".$this->zip."' "
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
     * @return boolean Vraci true, pokud byla entita uspesne smazana, jinak false
     */
    public function delete()
    {
        // Test, zda je ID platne
        if ($this->id <= 0) {
            return false;
        }        
        
        // Vlozime novy zaznam do DB
        $sql = "DELETE FROM `customer` WHERE `id` = '".$this->id."'";
        $res = $this->dbconn->query($sql);
        
        // Test, zda bylo neco vlozeno
        if ($res->result && !$res->error) {
            // Entita smazana, nyni je nutne ji odebrat z modelLoaderu!
            $this->loader->detach($this);
            
            // Jelikoz je nyni entita volna, nastavime ji ID na null
            $this->id = null;
            
            // Vraci OK
            return true;
        }
        
        return false;
    }
    
    
    /** ********************************************************************* */
    /** Gettery                                                               */
    /** ********************************************************************* */


    /**
     * Vraci krestni jmeno.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Vraci prijmeni.
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }
    
    
    /**
     * Vraci cele jmeno.
     *
     * @return string
     */
    public function GetFullName()
    {
        return $this->name . ' ' . $this->surname;
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
     * Vraci cislo op.
     *
     * @return string
     */
    public function getIdentityNumber()
    {
        return $this->identityNumber;
    }


    /**
     * Vraci ulici.
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }


    /**
     * Vraci mesto.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }


    /**
     * Vraci zemi puvodu.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }


    /**
     * Vraci psc.
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }


    /**
     * Vraci datum vytvoreni uctu.
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }


    /** ********************************************************************* */
    /** Settery                                                               */
    /** ********************************************************************* */


    /**
     * Nastavi krestni jmeno.
     *
     * @param string $name
     * @return CC_Customer
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
     * @return CC_Customer
     */
    public function setSurname($surname)
    {
        $this->surname = (string)$surname;
        return $this;
    }


    /**
     * Nastavi email
     *
     * @param string $email
     * @return CC_Customer
     */
    public function setEmail($email)
    {
        $this->email = (string)$email;
        return $this;
    }


    /**
     * Nastavi telefon.
     *
     * @param string $phone
     * @return CC_Customer
     */
    public function setPhone($phone)
    {
        $this->phone = (string)$phone;
        return $this;
    }


    /**
     * Nastavi cislo op.
     *
     * @param string $identityNumber
     * @return CC_Customer
     */
    public function setIdentityNumber($identityNumber)
    {
        $this->identityNumber = (string)$identityNumber;
        return $this;
    }


    /**
     * Nastavi ulici.
     *
     * @param string $street
     * @return CC_Customer
     */
    public function setStreet($street)
    {
        $this->street = (string)$street;
        return $this;
    }


    /**
     * Nastavi mesto.
     *
     * @param string $city
     * @return CC_Customer
     */
    public function setCity($city)
    {
        $this->city = (string)$city;
        return $this;
    }


    /**
     * Nastavi zemi puvodu.
     *
     * @param string $country
     * @return CC_Customer
     */
    public function setCountry($country)
    {
        $this->country = (string)$country;
        return $this;
    }


    /**
     * Nastavi pcs.
     *
     * @param string $zip
     * @return CC_Customer
     */
    public function setZip($zip)
    {
        $this->zip = (string)$zip;
        return $this;
    }
    
    
}
    