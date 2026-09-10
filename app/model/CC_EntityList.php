<?php
/**
 * CC_EntityList.php
 *
 * Kolekce reprezentujici seznam entit.
 *
 * @copyright 2014
 * @version 1.0.0
 */


abstract class CC_EntityList
{
    
    /**
     * Databazove pripojeni.
     * @var SM_Database
     */
    protected $dbconn = null;
    
    
    /**
     * Loader entit.
     * @var SM_Database
     */
    protected $loader = null;
    
    
    /**
     * Seznam vsech entit.
     * @var array
     */
    protected $list = array();
    
    
    protected $selectClause = array();
    protected $fromClause = array();
    protected $whereClause = array();
    protected $sortClause = array();
    
    
    /**
     * Celkovy pocet entit vyhovujicich dotazu.
     * @var int
     */
    protected $totalCount = null;
    
    
    /**
     * Vychozi kontruktor entity.
     * 
     * @param CC_Database $dbconn
     * @param int $id
     */
    public function __construct(CC_ModelLoader $loader, CC_Database $dbconn)
    {
        $this->loader = $loader;
        $this->dbconn = $dbconn;
    }
    
    
    /**
     * Ziska seznam vsech entit daneho typu, ktere vyhovuji filtru, serazenych 
     * podle sorteru. Pokud je zadan limit, vraci pouze tento pocet entit, pokud
     * je zadan i offset, vraci entity od tohoto mista. Pokud je zadan limit
     * vraci i celkovy pocet zaznamu nalezenych v DB (uklada do totalCount).
     * 
     * Vsechny entity rovnou vytvari.
     * 
     * @param array $filter Seznam vyhledavacih podminek (clausule WHERE)
     * @param array $sorter Seznam radicich podminek (clausule ORDER BY)
     * @param int $limit Omezeni poctu vracenych zaznamu
     * @param int $offset Urcite prvni zaznam od ktereho se bude vracet
     * @return array Vraci seznam nalezenych entit.
     */
    public function find(array $filter = array(), array $sorter = array(), $limit = null, $offset = null)
    {
        return array();
    }
    
    
    /**
     * Vraci celkovy pocet entit (pokud nebylo zahledano, vraci null).
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }
    
    
    /**
     * Sestavi SQL dotaz z jednotlivych klausuli, naplnenych v metode find().
     * 
     * @param int|null $limit
     * @param int|null $offset
     * @return string|null Vraci SQL dotaz pro pozdejsi pouziti, nebo NULL pokud doslo k chybe
     */
    protected function buildQuery()
    {
        $sql = '';
        
        // Klausule SELECT (pokud je prazdna, zadava pouze 'id').
        if (!empty($this->selectClause)) {
            $sql .= 'SELECT ' . join(', ', $this->selectClause);
        } else {
            $sql .= 'SELECT `id`';
        }
        
        // Klausule FROM (pokud je prazdna - chyba)
        if (!empty($this->fromClause)) {
            $sql .= ' FROM ' . join(', ', $this->fromClause);
        } else {
            return null;
        }
        
        // Klausule WHERE
        if (!empty($this->whereClause)) {
            $sql .= ' WHERE ' . join(' AND ', $this->whereClause);
        }
        
        // Klausule ORDER BY (vychozi rarezi je podle ID)
        if (!empty($this->sortClause)) {
            $sql .= ' ORDER BY ' . join(', ', $this->sortClause);
        } else {
            $sql .= ' ORDER BY  `id` ASC';
        }
        
        return $sql;
    }
    
    
    /**
     * Sestavi SQL dotaz z jednotlivych klausuli, naplnenych v metode find().
     * 
     * @param int|null $limit
     * @param int|null $offset
     * @return string|null Vraci SQL dotaz pro pozdejsi pouziti, nebo NULL pokud doslo k chybe
     */
    protected function executeQuery($sql, $limit = null, $offset = null, $pk = 'id')
    {
        // Prazdny dotaz nema cenu vykovavat
        if (empty($sql)) {
            return array();
        }
        
        // Pokud mame limit
        $sqlCount = null;
        if ($limit) {
            // Dotaz pro ziskani poctu vsech zaznamu bez limitu
            $sqlCount = "SELECT count(*) as cnt FROM ($sql) AS foo";
            
            // Pokud mame i offset
            if ($offset) {
                $sql .= ' LIMIT ' . $offset . ', ' . $limit;
            } else {
                $sql .= ' LIMIT ' . $limit;
            }
        }
        
        
        // Provedeme dotaz a ziskame data
        $res = $this->dbconn->query($sql);
        if ($res->numRows > 0) {
            // Nazev entity
            preg_match('#(\w+)List$#', get_class($this), $m);
            $className = $m[1];
            
            // Vytvorime entity
            $entities = array();
            while (($row = $this->dbconn->fetch($res->result))) {
                $entities[] = $this->loader->get($className, $row->$pk);
            }
            
            // Pokud je definovan limit, ziskame pocet vsechn entity
            if ($sqlCount) {
                $res = $this->dbconn->query($sqlCount);
                if ($res->numRows > 0) {
                    $row = $this->dbconn->fetch($res->result);
                    $this->totalCount = $row->cnt;
                }
            } else {
                // Jinak si do total count poznacime pocet vsech nactenych entit
                $this->totalCount = count($entities);
            }
            
            // Vyprazdnime pole klausuli pro dalsi pouziti
            $this->selectClause = array();
            $this->fromClause = array();
            $this->whereClause = array();
            $this->sortClause = array();
            
            // Vratime seznam vsech entit
            return $entities;
        } 
        
        
        return array();
    }
    
    
}
