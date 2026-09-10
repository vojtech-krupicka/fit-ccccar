<?php
/**
 * CC_Loan.php
 *
 * Entita reprezentujici vypujcku vozidla. Odpovida tabulce 'loan'.
 *
 * @copyright 2014
 * @version 1.0.0
 */

final class CC_Loan extends CC_Entity
{

    /** @var CC_Vehicle */
    private $vehicle = NULL;


    /** @var CC_Customer */
    private $customer = NULL;


    /** @var CC_Employee */
    private $borrower = NULL;


    /** @var CC_Employee */
    private $recipient = NULL;

    
    /** @var CC_RentalOffice */
    private $rentalOffice = NULL;


    /** @var DateTime */
    private $reservedAt = NULL;


    /** @var DateTime */
    private $reservedFrom = NULL;


    /** @var DateTime */
    private $reservedTo = NULL;


    /** @var DateTime */
    private $startAt = NULL;


    /** @var DateTime */
    private $endAt = NULL;


    /** @var float */
    private $distance = 0.0;


    /** @var float */
    private $price = 0.0;
    
    
    /** @var boolean */
    private $deleted = false;


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
        $sql = "SELECT `vehicle_id`, `customer_id`, `borrower_id`, `recipient_id`, `rentalOffice_id`,"
                ." `reservedAt`, `reservedFrom`, `reservedTo`, `startAt`, `endAt`,"
                ." `distance`, `price`, `deleted`"
                ." FROM `loan` WHERE `id` = '" . $this->id . "'";
        $res = $this->dbconn->query($sql);
        
        // Test, zda byl skutecne vracen pouze 1 radek
        if ($res->numRows == 1) {
            $row = $this->dbconn->fetch($res->result);
                
            // Naplni instanci daty z DB
            $this->reservedAt = new DateTime($row->reservedAt);
            $this->reservedFrom = new DateTime($row->reservedFrom);
            $this->reservedTo = new DateTime($row->reservedTo);
            $this->startAt = ($row->startAt) ? new DateTime($row->startAt) : null;
            $this->endAt = ($row->endAt) ? new DateTime($row->endAt) : null;
            $this->distance = ($row->distance) ? $row->distance : null;
            $this->price = ($row->price) ? $row->price : null;
            $this->deleted = ($row->deleted == '1') ? true : false;
            
            $this->vehicle = ($row->vehicle_id) ? $this->loader->get('CC_Vehicle', $row->vehicle_id) : null;
            $this->customer = ($row->customer_id) ? $this->loader->get('CC_Customer', $row->customer_id) : null;
            $this->borrower = ($row->borrower_id) ? $this->loader->get('CC_Employee', $row->borrower_id) : null;
            $this->recipient = ($row->recipient_id) ? $this->loader->get('CC_Employee', $row->recipient_id) : null;
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
        // Test, zda jsou zadany vsechny dulezite udaje
        if (!$this->vehicle || !$this->vehicle->getId()) {
            return false;
        }
        if (!$this->customer || !$this->customer->getId()) {
            return false;
        }
        if (!$this->rentalOffice || !$this->rentalOffice->getId()) {
            return false;
        }
        if (!$this->reservedAt || !($this->reservedAt instanceof DateTime)) {
            return false;
        }
        if (!$this->reservedFrom || !($this->reservedFrom instanceof DateTime)) {
            return false;
        }
        if (!$this->reservedTo || !($this->reservedTo instanceof DateTime)) {
            return false;
        }
        
        // Vlozime novy zaznam do DB
        $sql = "INSERT INTO `loan` ("
                ." `vehicle_id`, `customer_id`, `rentalOffice_id`,"
                ." `reservedAt`, `reservedFrom`, `reservedTo`) VALUES ("
                ." '" . $this->vehicle->getId() . "', '" . $this->customer->getId() . "'"
                .", '" . $this->rentalOffice->getId() . "', '" . $this->reservedAt->format('Y-m-d H:i:s') . "'"
                .", '" . $this->reservedFrom->format('Y-m-d') . "', '" . $this->reservedTo->format('Y-m-d') . "')";
        
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
        if (!$this->vehicle || !$this->vehicle->getId()) {
            return false;
        }
        if (!$this->customer || !$this->customer->getId()) {
            return false;
        }
        if (!$this->rentalOffice || !$this->rentalOffice->getId()) {
            return false;
        }
        if (!$this->reservedAt || !($this->reservedAt instanceof DateTime)) {
            return false;
        }
        if (!$this->reservedFrom || !($this->reservedFrom instanceof DateTime)) {
            return false;
        }
        if (!$this->reservedTo || !($this->reservedTo instanceof DateTime)) {
            return false;
        }
        
        // Vlozime novy zaznam do DB
        $sql = "UPDATE `loan` SET "
                ." `vehicle_id` = '".$this->vehicle->getId()."', "
                ." `rentalOffice_id` = '".$this->rentalOffice->getId()."', "
                ." `customer_id` = '".$this->customer->getId()."', "
                ." `borrower_id` = ".($this->borrower ? "'".$this->borrower->getId()."'" : "NULL").", "
                ." `recipient_id` = ".($this->recipient ? "'".$this->recipient->getId()."'" : "NULL").", "
                ." `reservedAt` = '".$this->reservedAt->format('Y-m-d H:i:s')."', "
                ." `reservedFrom` = '".$this->reservedFrom->format('Y-m-d')."', "
                ." `reservedTo` = '".$this->reservedTo->format('Y-m-d')."', "
                ." `startAt` = ".($this->startAt ? "'".$this->startAt->format('Y-m-d H:i:s')."'" : "NULL").", "
                ." `endAt` = ".($this->endAt ? "'".$this->endAt->format('Y-m-d H:i:s')."'" : "NULL").", "
                ." `distance` = ".($this->distance ? "'".$this->distance."'" : "NULL").", "
                ." `price` = ".($this->price ? "'".$this->price."'" : "NULL").", "
                ." `deleted` = '".($this->deleted ? '1' : '0')."'"
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
        $sql = "DELETE FROM `loan` WHERE `id` = '".$this->id."'";
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
     * Vraci vypujcene vozidlo.
     * 
     * @return CC_Vehicle
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }


    /**
     * Vraci zakaznika.
     * 
     * @return CC_Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }


    /**
     * Vraci zamestnance, ktery vozidlo zapujcil.
     * 
     * @return CC_Employee|null
     */
    public function getBorrower()
    {
        return $this->borrower;
    }


    /**
     * Vraci zamestnance, ktery vypujcku ukoncil (prijal zpet vozidlo).
     * 
     * @return CC_Employee|null
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    
    /**
     * Vraci pobocku, na ktere bude auto pripraveno k vyzvednuti.
     * 
     * @return CC_RentalOffice
     */
    public function getRentalOffice()
    {
        return $this->rentalOffice;
    }
    

    /**
     * Vraci cas rezervace.
     * 
     * @return DateTime
     */
    public function getReservedAt()
    {
        return $this->reservedAt;
    }


    /**
     * Vraci zacatek rezervace.
     * 
     * @return DateTime
     */
    public function getReservedFrom()
    {
        return $this->reservedFrom;
    }

    
    /**
     * Vraci konec rezervace.
     * 
     * @return DateTime
     */
    public function getReservedTo()
    {
        return $this->reservedTo;
    }


    /**
     * Vraci zacatek vypujcky.
     * 
     * @return DateTime|null
     */
    public function getStartAt()
    {
        return $this->startAt;
    }


    /**
     * Vraci konec vypujcky.
     * 
     * @return DateTime|null
     */
    public function getEndAt()
    {
        return $this->endAt;
    }


    /**
     * Vraci ujetou vzdalenost.
     * 
     * @return float|null
     */
    public function getDistance()
    {
        return $this->distance;
    }


    /**
     * Vraci celkovou cenu vypujcky.
     * 
     * @return float|null
     */
    public function getPrice()
    {
        return $this->price;
    }
    
    
    /**
     * Vraci priznak zda byla vypujcka stornovana.
     * 
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }


    /** ********************************************************************* */
    /** Settery                                                               */
    /** ********************************************************************* */


    /**
     * Nastavi vozidlo, ktere si zakaznik vypujcil.
     * 
     * @param CC_Vehicle $vehicle
     * @return CC_Loan
     */
    public function setVehicle(CC_Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;
        return $this;
    }


    /**
     * Nastavi zakaznika.
     * 
     * @param CC_Customer $customer
     * @return CC_Loan
     */
    public function setCustomer(CC_Customer $customer)
    {
        $this->customer = $customer;
        return $this;
    }


    /**
     * Nastavi zamestnance, ktery vypujcku vyrizuje (pujcuje)
     * 
     * @param CC_Employee|null $borrower
     * @return CC_Loan
     */
    public function setBorrower(CC_Employee $borrower = null)
    {
        $this->borrower = $borrower;
        return $this;
    }


    /**
     * Nastavi zamestnance, ktery vypujcku ukoncuje.
     * 
     * @param CC_Employee|null $recipient
     * @return CC_Loan
     */
    public function setRecipient(CC_Employee $recipient = null)
    {
        $this->recipient = $recipient;
        return $this;
    }
    
    
    /**
     * Nastavi pobocku pro vyzvednuti vozidla.
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
     * Nastavi cas rezervace.
     * 
     * @param DateTime $reservedAt
     * @return CC_Loan
     */
    public function setReservedAt(DateTime $reservedAt)
    {
        $this->reservedAt = $reservedAt;
        return $this;
    }
    
    
    /**
     * Nastavi zacatek rezervace.
     * 
     * @param DateTime $reservedFrom
     * @return CC_Loan
     */
    public function setReservedFrom(DateTime $reservedFrom)
    {
        $this->reservedFrom = $reservedFrom;
        return $this;
    }
    
    
    /**
     * Nastavi konec rezervace.
     * 
     * @param DateTime $reservedTo
     * @return CC_Loan
     */
    public function setReservedTo(DateTime $reservedTo)
    {
        $this->reservedTo = $reservedTo;
        return $this;
    }


    /**
     * Nastavi zacatek vypujcky (pokud vypujcka nezacala, null)
     * 
     * @param DateTime $startAt
     * @return CC_Loan
     */
    public function setStartAt(DateTime $startAt = null)
    {
        $this->startAt = $startAt;
        return $this;
    }


    /**
     * Nastavi konec vypujcky (pokud vypujcka neskoncila, null)
     * 
     * @param DateTime $endAt
     * @return CC_Loan
     */
    public function setEndAt(DateTime $endAt = null)
    {
        $this->endAt = $endAt;
        return $this;
    }


    /**
     * Nastavi nastavi ujetou vzdalenost.
     * 
     * @param float $distance
     * @return CC_Loan
     */
    public function setDistance($distance)
    {
        $this->distance = ($distance === null) ? null : (float)$distance;
        return $this;
    }


    /**
     * Nastavi celkovou cenu vypujcky.
     * 
     * @param float $price
     * @return CC_Loan
     */
    public function setPrice($price)
    {
        $this->price = ($price === null) ? null : (float)$price;
        return $this;
    }
    
    
    /**
     * Nastavi priznak, zda je vypujcka zrusena.
     * 
     * @param boolean $flag
     * @return CC_Loan
     */
    public function setDeleted($flag)
    {
        $this->deleted = (bool)$flag;
        return $this;
    }


}
