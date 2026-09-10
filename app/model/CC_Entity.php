<?php
/**
 * CC_Entity.php
 *
 * Abstraktni definice entity. Kazda entita ma svoje ID.
 *
 * @copyright 2014
 * @version 1.0.0
 */

abstract class CC_Entity
{
    /**
     * Loader entit.
     * @var CC_ModelLoader
     */
    protected $loader = null;
    
    
    /**
     * Databazove pripojeni.
     * @var SM_Database
     */
    protected $dbconn = null;
    
    
    /** 
     * Unikatni ID entity pro provazani s DB radkem.
     * @var int 
     */
    protected $id = null;
    
    
    /**
     * Vychozi kontruktor entity.
     * 
     * @param CC_Database $dbconn
     * @param int $id
     */
    public function __construct(CC_ModelLoader $loader, CC_Database $dbconn, $id = null)
    {
        $this->loader = $loader;
        $this->dbconn = $dbconn;
        $this->id = ($id) ? (int)$id : null;
    }
    
    
    /**
     * Testuje, zda entita s pozadovanym ID existuje.
     * 
     * @param CC_Database $dbconn
     * @param int $id
     * @return boolean
     */
    public static function exists(CC_Database $dbconn, $id) 
    {
        // Magicky ziskame jmeno volajici tridy, ze jmena odstranime 'CC_'
        // a prevedeme prvni pismeno na male. V tomto formatu jsou vsechny tablky v DB.
        preg_match('#CC_(\w+)$#', get_called_class(), $m);
        $table = lcfirst($m[1]);
        
        // Vytvorime dotaz (pouze pocet).
        $sql = "SELECT count(*) AS cnt FROM `$table` WHERE `id` = '$id'";
        $res = $dbconn->query($sql);
        
        // A pokud je pocet zaznamu prave 1 - je to OK (ID jsou vzdy unikatni).
        if ($res->numRows == 1) {
            $row = $dbconn->fetch($res->result);
            if ((int)$row->cnt == 1) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Pokud je entita inicializovana (ma definovano ID), nacte a nastavi
     * vsechny jeji data z DB.
     * 
     * @param array $filter Pri nacitani lze filtrovat kde klicem je sloupec.
     * @return boolean Vraci true, pokud byla entita nactena v poradku, jinak false.
     */
    public function load(array $filter = array())
    {
        return false;
    }
    
    
    /**
     * Ulozi entitu do DB. Pokud neni ID zadano, vytvori novy zaznam v DB,
     * jinak aktualizuje stavajici.
     * 
     * @return boolean Vraci true, pokud byla entita ulozena v poradku, jinak false.
     */
    public function persist()
    {
        // Pokud je id zadano aktualizuje data, jinak vytvori novy zaznam
        if ($this->id > 0 && self::exists($this->dbconn, $this->id)) {
            return $this->update();
        } else {
            // Vytvori novou entitu a prida ji do loaderu
            if ($this->create() && $this->loader->attach($this)) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Pokud je entita inicializovana (ma definovano ID) smaze ji z DB.
     * 
     * @return boolean Vraci true, pokud byla entita smazana v poradku, jinak false.
     */
    public function delete()
    {
        return false;
    }
    
    
    /**
     * Vytvori novou entitu v DB.
     * 
     * @return boolean Vraci true, pokud byla entita uspesne vytvorena v DB, jinak false.
     */
    protected function create()
    {
        return false;
    }
    
    
    /**
     * Aktualizuje stavajici entitu v DB.
     * 
     * @return boolean Vraci true, pokud byla entita uspesne aktualizovana v DB, jinak false.
     */
    protected function update()
    {
        return false;
    }
    
    
    /**
     * Vraci ID entity.
     * 
     * @return int
     */
    public function getId() 
    {
        return $this->id;
    }
}
    