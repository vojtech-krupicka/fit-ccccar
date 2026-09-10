<?php
/**
 * CC_Vehicle.php
 *
 * Entita reprezentujici vozidlo. Odpovida tabulce 'vehicle'.
 *
 * @copyright 2014
 * @version 1.0.0
 */

final class CC_Vehicle extends CC_Entity
{

    /** @var CC_VehicleType */
    private $vehicleType = NULL;


    /** @var CC_VehicleFuelType */
    private $vehicleFuelType = NULL;


    /** @var CC_VehicleColor */
    private $vehicleColor = NULL;


    /** @var CC_RentalOffice */
    private $rentalOffice = NULL;


    /** @var string */
    private $model = NULL;


    /** @var string */
    private $plateNumber = NULL;


    /** @var float */
    private $mileage = 0.0;


    /** @var int */
    private $year = 0;
    
    
    /** @var float */
    private $costPerDay = 0.0;


    /** @var float */
    private $costPerUnit = 0.0;


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
        $sql = "SELECT * FROM `vehicle` WHERE `id` = '" . $this->id . "'";
        $res = $this->dbconn->query($sql);
        
        // Test, zda byl skutecne vracen pouze 1 radek
        if ($res->numRows == 1) {
            $row = $this->dbconn->fetch($res->result);
                
            // Naplni instanci daty z DB
            $this->model = $row->model;
            $this->plateNumber = $row->plateNumber;
            $this->mileage = (float)$row->mileage;
            $this->year = (int)$row->year;
            $this->costPerDay = (float)$row->costPerDay;
            $this->costPerUnit = (float)$row->costPerUnit;
            
            $this->vehicleType = ($row->vehicleType_id) ? $this->loader->get('CC_VehicleType', $row->vehicleType_id) : null;
            $this->vehicleFuelType = ($row->vehicleFuelType_id) ? $this->loader->get('CC_VehicleFuelType', $row->vehicleFuelType_id) : null;
            $this->vehicleColor = ($row->vehicleColor_id) ? $this->loader->get('CC_VehicleColor', $row->vehicleColor_id) : null;
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
        $sql = "INSERT INTO `vehicle` (...) VALUES (...)";
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
        
        // Test, zda jsou zadany vsechny dulezite udaje
        if (!$this->vehicleType || !$this->vehicleType->getId()) {
            return false;
        }
        if (!$this->vehicleFuelType || !$this->vehicleFuelType->getId()) {
            return false;
        }
        if (!$this->vehicleColor || !$this->vehicleColor->getId()) {
            return false;
        }
        if (!$this->rentalOffice || !$this->rentalOffice->getId()) {
            return false;
        }
        
        // Vlozime novy zaznam do DB
        $sql = "UPDATE `vehicle` SET "
                ." `vehicleType_id` = '".$this->vehicleType->getId()."', " 
                ." `vehicleFuelType_id` = '".$this->vehicleFuelType->getId()."', " 
                ." `vehicleColor_id` = '".$this->vehicleColor->getId()."', " 
                ." `rentalOffice_id` = '".$this->rentalOffice->getId()."', " 
                ." `model` = '".$this->model."', " 
                ." `plateNumber` = '".$this->plateNumber."', " 
                ." `mileage` = '".$this->mileage."', " 
                ." `year` = '".$this->year."', " 
                ." `costPerDay` = '".$this->costPerDay."', " 
                ." `costPerUnit` = '".$this->costPerUnit."'" 
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
        $sql = "DELETE FROM `vehicle` WHERE `id` = '".$this->id."'";
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
     * Vraci typ vozidla.
     * 
     * @return CC_VehicleType
     */
    public function getVehicleType()
    {
        return $this->vehicleType;
    }


    /**
     * Vraci typ paliva.
     * 
     * @return CC_VehicleFuelType
     */
    public function getVehicleFuelType()
    {
        return $this->vehicleFuelType;
    }


    /**
     * Vraci barvu.
     * 
     * @return CC_VehicleColor
     */
    public function getVehicleColor()
    {
        return $this->vehicleColor;
    }


    /**
     * Vraci pobocku, ke ktere je vozidlo vazano.
     * 
     * @return CC_RentalOffice
     */
    public function getRentalOffice()
    {
        return $this->rentalOffice;
    }


    /**
     * Vraci model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }
    
    
    /**
     * Vraci cely nazev vozidla jako 'Vyrobce znacka model'.
     * 
     * @return string
     */
    public function getFullName()
    {
        return $this->vehicleType->getManufacturer() . ' ' . $this->vehicleType->getBrand() . ' ' . $this->model;
    }


    /**
     * Vraci SPZ.
     * 
     * @return string
     */
    public function getPlateNumber()
    {
        return $this->plateNumber;
    }


    /**
     * Vraci celkovy pocet km.
     * 
     * @return float
     */
    public function getMileage()
    {
        return $this->mileage;
    }


    /**
     * Vraci rok vyroby.
     * 
     * @return 
     */
    public function getYear()
    {
        return $this->year;
    }
    
    
    /**
     * Vraci cenu za den.
     * 
     * @return float
     */
    public function getCostPerDay()
    {
        return $this->costPerDay;
    }
    
    
    /**
     * Vraci cenu za jednotku vzdalenosti (km).
     * 
     * @return float
     */
    public function getCostPerUnit()
    {
        return $this->costPerUnit;
    }


    /** ********************************************************************* */
    /** Settery                                                               */
    /** ********************************************************************* */


    /**
     * Nastavi typ vozidla.
     * 
     * @param CC_VehicleType $vehicleType
     * @return CC_Vehicle
     */
    public function setVehicleType(CC_VehicleType $vehicleType)
    {
        $this->vehicleType = $vehicleType;
        return $this;
    }


    /**
     * Nastavi typ paliva.
     * 
     * @param CC_VehicleFuelType $vehicleFuelType
     * @return CC_Vehicle
     */
    public function setVehicleFuelType(CC_VehicleFuelType $vehicleFuelType)
    {
        $this->vehicleFuelType = $vehicleFuelType;
        return $this;
    }


    /**
     * Nastavi barvu.
     * 
     * @param CC_VehicleColor $vehicleColor
     * @return CC_Vehicle
     */
    public function setVehicleColor(CC_VehicleColor $vehicleColor)
    {
        $this->vehicleColor = $vehicleColor;
        return $this;
    }


    /**
     * Nastavi pobocku.
     * 
     * @param CC_RentalOffice $rentalOffice
     * @return CC_Vehicle
     */
    public function setRentalOffice(CC_RentalOffice $rentalOffice)
    {
        $this->rentalOffice = $rentalOffice;
        return $this;
    }


    /**
     * Nastavi model.
     * 
     * @param string $model
     * @return CC_Vehicle
     */
    public function setModel($model)
    {
        $this->model = (string)$model;
        return $this;
    }


    /**
     * Nastavi SPZ.
     * 
     * @param string $plateNumber
     * @return CC_Vehicle
     */
    public function setPlateNumber($plateNumber)
    {
        $this->plateNumber = (string)$plateNumber;
        return $this;
    }


    /**
     * Nastavi pocet km.
     * 
     * @param float $mileage
     * @return CC_Vehicle
     */
    public function setMileage($mileage)
    {
        $this->mileage = (float)$mileage;
        return $this;
    }


    /**
     * Nastavi rok vyroby.
     * 
     * @param int $year
     * @return CC_Vehicle
     */
    public function setYear($year)
    {
        $this->year = (int)$year;
        return $this;
    }


    /**
     * Nastavi cenu za den.
     * 
     * @param float $cost
     * @return CC_Vehicle
     */
    public function setCostPerDay($cost)
    {
        $this->costPerDay = (float)$cost;
        return $this;
    }
    
    
    /**
     * Nastavi cenu za jednotku vzdalenosti (km).
     * 
     * @param float $cost
     * @return CC_Vehicle
     */
    public function setCostPerUnit($cost)
    {
        $this->costPerUnit = (float)$cost;
        return $this;
    }
    
    
}

