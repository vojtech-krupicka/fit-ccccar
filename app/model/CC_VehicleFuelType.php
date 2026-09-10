<?php
/**
 * CC_VehicleFuelType.php
 *
 * Entita reprezentujici typ paliva. Odpovida tabulce 'vehicleFuelType'.
 *
 * @copyright 2014
 * @version 1.0.0
 */

final class CC_VehicleFuelType extends CC_Entity
{

    /** @var string */
    private $fuel = NULL;


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
        $sql = "SELECT * FROM `vehicleFuelType` WHERE `id` = '" . $this->id . "'";
        $res = $this->dbconn->query($sql);
        
        // Test, zda byl skutecne vracen pouze 1 radek
        if ($res->numRows == 1) {
            $row = $this->dbconn->fetch($res->result);
                
            // Naplni instanci daty z DB
            $this->fuel = $row->fuel;
            
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
        $sql = "INSERT INTO `vehicleFuelType` (...) VALUES (...)";
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
        $sql = "UPDATE `vehicleFuelType` SET ... WHERE `id` = '".$this->id."'";
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
        $sql = "DELETE FROM `vehicleFuelType` WHERE `id` = '".$this->id."'";
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
     * Vraci typ paliva.
     * 
     * @return string
     */
    public function getFuel()
    {
        return $this->fuel;
    }


    /** ********************************************************************* */
    /** Settery                                                               */
    /** ********************************************************************* */


    /**
     * Nastavi typ paliva.
     * 
     * @param string $fuel
     * @return CC_VehicleFuelType
     */
    public function setFuel($fuel)
    {
        $this->fuel = (string)$fuel;
        return $this;
    }


}

