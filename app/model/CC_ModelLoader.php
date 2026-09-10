<?php
/**
 * CC_ModelLoader.php
 *
 * Trida, ktera se stara o vsechny instance vsech entit. Jedna se tzv. Service
 * Locator. Tato trida poskytuje instance entit proto, aby bylo zajisteno, 
 * ze kazda entita (s definovanym ID) je instanciovana vzdy prave jednou.
 * 
 * ModelLoader poskytuje tyto metody:
 *      - create:   vytvori novou instanci entity bez ID.
 *      - get:      vraci instanci entity s danym ID, pokud entity s danym ID 
 *                  neni prozatim instanciovana, vytvori ji.
 *      - attach:   prida novou entitu do loaderu, pridani je mozne pouze pokud
 *                  ma entita jiz sve ID a soucasne neni v seznamu jiz 
 *                  instanciovanych entit.
 *
 * @copyright 2014
 * @version 1.0.0
 */


final class CC_ModelLoader
{
    /**
     * Databazove pripojeni.
     * @var SM_Database
     */
    private $dbconn = null;
    
    
    /**
     * Seznam vsech entit.
     * @var array
     */
    private $pool = array();
    
    
    /**
     * Konstruktor.
     * 
     * @param CC_Database $dbconn
     */
    public function __construct(CC_Database $dbconn)
    {
        $this->dbconn = $dbconn;
    }
    
 
    /**
     * Vytvari novou entitu bez ID. Preda ji vsechny prislusne zavislosti 
     * (tedy pripojeni k DB a sebe sama).
     * 
     * @param string $class
     * @return CC_Entity|NULL Vraci instanci entity nebo NULL, pokud trida neexistuje
     */
    public function create($class)
    {
        if (class_exists($class)) {
            return new $class($this, $this->dbconn); 
        }
        
        return null;
    }
    
    
    /**
     * Vytvari novy seznam entit. Preda ji vsechny zavislosti.
     * 
     * @param string $class
     * @return CC_EntityList|null
     */
    public function createList($class)
    {
        $fullClassName = $class . 'List';
        if (class_exists($fullClassName)) {
            return new $fullClassName($this, $this->dbconn); 
        }
        
        return null;
    }
    
    
    /**
     * Vraci instanci entity. Pokud byla entita s danym ID jiz instancionvana, 
     * vraci prave tuto instanci, pokud entita s pozadovanym ID neexistuje,
     * nebo je nazev tridy neplatny, vraci NULL.
     * 
     * @param type $class
     * @param type $id
     * @return CC_Entity|null Vraci instanci entity s pozadovanym ID, nebo NULL, pokud trida neexistuje
     */
    public function get($class, $id)
    {
        // Pokud trida existuje
        if (class_exists($class)) {
            // Instance tridy jiz byla vytvorena
            if (isset($this->pool[$class][$id])) {
                return $this->pool[$class][$id];
            } 
            // Overime zda skutecne existuje zaznam s danym ID
            else if ($class::exists($this->dbconn, $id)) {
                // Instance pro dane ID zatim neexistuje, vytvori se
                $entity = new $class($this, $this->dbconn, $id);
                if ($entity) {
                    $this->pool[$class][$id] = $entity;
                
                    if ($entity->load()) {
                        return $entity;
                    } else {
                        unset($this->pool[$class][$id]);
                    }
                }
            }
        }
        
        // Trida neni platna, nebo entita s danym ID neni v DB, vraci NULL
        return null;
    }
    
    
    /**
     * Prida novou instanci entity do seznamu nactenych entit. Entita musi 
     * platne ID.
     * v metode CC_Entity::create().
     * 
     * @param CC_Entity $entity
     * @return boolean Vraci true, pokud byla entita uspesne pridana do seznamu, jiank false
     */
    public function attach(CC_Entity $entity)
    {
        $id = $entity->getId();
        $class = get_class($entity);
        
        // Pokud ma entita nastaveno ID a soucasne neni zatim v seznamu
        if ($id > 0 && !isset($this->pool[$class][$id])) {
            $this->pool[$class][$id] = $entity;
            return true;
        }
        
        return false;
    }
    
    
    /**
     * Odebere instanci entity ze seznamu nactenych entit. Entita musi mit 
     * platne ID.
     * 
     * @param CC_Entity $entity
     * @return boolean Vraci true, pokud byla entita uspesne odebrana ze seznamu, jiank false
     */
    public function detach(CC_Entity $entity)
    {
        $id = $entity->getId();
        $class = get_class($entity);
        
        // Pokud ma entita nastaveno ID a soucasne je v seznamu
        if ($id > 0 && isset($this->pool[$class][$id])) {
            unset($this->pool[$class][$id]);
            return true;
        }
        
        return false;
    }
    
    
}