<?php
/**
 * CC_RentalOffice.php
 *
 * Entita reprezentujici pobocku pujcovny. Odpovida tabulce 'rentalOffice'.
 *
 * @copyright 2014
 * @version 1.0.0
 */

final class CC_RentalOffice extends CC_Entity
{

    /** @var CC_Employee */
    private $supervisor = NULL;


    /** @var string */
    private $name = NULL;


    /** @var string */
    private $street = NULL;


    /** @var string */
    private $city = NULL;


    /** @var string */
    private $country = NULL;


    /** @var string */
    private $zip = NULL;


    /** @var string */
    private $phone = NULL;


    /** @var string */
    private $email = NULL;
    
    
    /** @var float */
    private $lat = NULL;
    
    
    /** @var float */
    private $lon = NULL;


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
        $sql = "SELECT `supervisor_id`, `name`, `street`, `city`, `country`, `zip`"
                ." , `phone`, `email`, `lat`, `lon`"
                ." FROM `rentalOffice` WHERE `id` = '" . $this->id . "'";
        $res = $this->dbconn->query($sql);
        
        // Test, zda byl skutecne vracen pouze 1 radek
        if ($res->numRows == 1) {
            $row = $this->dbconn->fetch($res->result);
                
            // Naplni instanci daty z DB
            $this->name = $row->name;
            $this->street = $row->street;
            $this->city = $row->city;
            $this->country = $row->country;
            $this->zip = $row->zip;
            $this->phone = $row->phone;
            $this->email = $row->email;
            $this->lat = (float)$row->lat;
            $this->lon = (float)$row->lon;
            
            $this->supervisor = ($row->supervisor_id) ? $this->loader->get('CC_Employee', $row->supervisor_id) : null;
            
            
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
        $sql = "INSERT INTO `rentalOffice` (...) VALUES (...)";
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
        $sql = "UPDATE `rentalOffice` SET ... WHERE `id` = '".$this->id."'";
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
        $sql = "DELETE FROM `rentalOffice` WHERE `id` = '".$this->id."'";
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
     * Vraci vedouciho pobocky (zamestanec).
     * 
     * @return CC_Employee
     */
    public function getSupervisor()
    {
        return $this->supervisor;
    }


    /**
     * Vraci nazev pobocky.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Vraci stat.
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
     * Vraci telefon.
     * 
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }


    /**
     * Vraci email na pobocku.
     * 
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    
    /**
     * Vraci zemepisnout sirku pobocky.
     * 
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }
    
    
    /**
     * Vraci zemepisnout delku pobocky.
     * 
     * @return float
     */
    public function getLon()
    {
        return $this->lon;
    }
    


    /** ********************************************************************* */
    /** Settery                                                               */
    /** ********************************************************************* */


    /**
     * Nastavi vedouciho.
     * 
     * @param CC_Employee $supervisor
     * @return CC_RentalOffice
     */
    public function setSupervisor(CC_Employee $supervisor)
    {
        $this->supervisor = $supervisor;
        return $this;
    }


    /**
     * Nastavi nazev pobocky.
     * 
     * @param string $name
     * @return CC_RentalOffice
     */
    public function setName($name)
    {
        $this->name = (string)$name;
        return $this;
    }


    /**
     * Nastavi ulici.
     * 
     * @param string $street
     * @return CC_RentalOffice
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
     * @return CC_RentalOffice
     */
    public function setCity($city)
    {
        $this->city = (string)$city;
        return $this;
    }


    /**
     * Nastavi stat.
     * 
     * @param string $country
     * @return CC_RentalOffice
     */
    public function setCountry($country)
    {
        $this->country = (string)$country;
        return $this;
    }


    /**
     * Nastavi psc.
     * 
     * @param string $zip
     * @return CC_RentalOffice
     */
    public function setZip($zip)
    {
        $this->zip = (string)$zip;
        return $this;
    }


    /**
     * Nastavi telefon.
     * 
     * @param string $phone
     * @return CC_RentalOffice
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
     * @return CC_RentalOffice
     */
    public function setEmail($email)
    {
        $this->email = (string)$email;
        return $this;
    }

    
    /**
     * Nastavi zemepisnou delku.
     * 
     * @param float $lat
     * @return CC_RentalOffice
     */
    public function setLat($lat)
    {
        $this->lat = (float)$lat;
        return $this;
    }
    
    
    /**
     * Nastavi zemepisnou sirku.
     * 
     * @param float $lon
     * @return CC_RentalOffice
     */
    public function setLon($lon)
    {
        $this->lon = (float)$lon;
        return $this;
    }
    

}

