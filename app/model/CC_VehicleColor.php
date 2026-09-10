<?php
/**
 * CC_VehicleColor.php
 *
 * Entita reprezentujici barvu vozidla. Odpovida tabulce 'vehicleColor'.
 *
 * @copyright 2014
 * @version 1.0.0
 */

final class CC_VehicleColor extends CC_Entity
{

    /** @var string */
    private $name = NULL;


    /** @var string */
    private $ean = NULL;


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
        $sql = "SELECT * FROM `vehicleColor` WHERE `id` = '" . $this->id . "'";
        $res = $this->dbconn->query($sql);
        
        // Test, zda byl skutecne vracen pouze 1 radek
        if ($res->numRows == 1) {
            $row = $this->dbconn->fetch($res->result);
                
            // Naplni instanci daty z DB
            $this->name = $row->name;
            $this->ean = $row->ean;
            
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
        $sql = "INSERT INTO `vehicleColor` (...) VALUES (...)";
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
        $sql = "UPDATE `vehicleColor` SET ... WHERE `id` = '".$this->id."'";
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
        $sql = "DELETE FROM `vehicleColor` WHERE `id` = '".$this->id."'";
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
     * Vraci nazev barvy.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Vraci kod barvy udavany vyrobcem.
     * 
     * @return string
     */
    public function getEan()
    {
        return $this->ean;
    }


    /** ********************************************************************* */
    /** Settery                                                               */
    /** ********************************************************************* */


    /**
     * Nastavi nazev barvy
     * 
     * @param string $name
     * @return CC_VehicleColor
     */
    public function setName($name)
    {
        $this->name = (string)$name;
        return $this;
    }


    /**
     * Nastavi kod barvy udavany vyrobcem
     * 
     * @param string $ean
     * @return CC_VehicleColor
     */
    public function setEan($ean)
    {
        $this->ean = (string)$ean;
        return $this;
    }


}

