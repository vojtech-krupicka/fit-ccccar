<?php
/**
 * CC_CustomerList.php
 *
 * Kolekce reprezentujici seznam entit typu zakaznik.
 *
 * @copyright 2014
 * @version 1.0.0
 */


final class CC_CustomerList extends CC_EntityList
{
    
    /**
     * Nalezne vsechny vypujcky.
     * 
     * @see CC_EntityList::find()
     * @param array $filter
     * @param array $sorter
     * @param int $limit
     * @param int $offset
     * @return array()
     */
    public function find(array $filter = array(), array $sorter = array(), $limit = null, $offset = null)
    {
        // Definujeme jednotlive clausule
        $this->selectClause[] = "`id`";
        $this->fromClause[] = "`customer` AS `c`";
                
        // Radime
        $this->sortClause = $sorter;
        
        // Zavolame funkci, ktera sestavi dotaz
        $sql = $this->buildQuery();
        
        // Zavolame vykonani dotazu
        $result = $this->executeQuery($sql, $limit, $offset, 'id');
        
        // Vracime vysledek
        return $result;        
    }
    
}