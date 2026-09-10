<?php
/**
 * CC_RentalOfficeList.php
 *
 * Kolekce reprezentujici seznam entit typu pobocka.
 *
 * @copyright 2014
 * @version 1.0.0
 */


final class CC_RentalOfficeList extends CC_EntityList
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
        $this->fromClause[] = "`rentalOffice` AS `ro`";
                
        // Radime
        $this->sortClause = $sorter;
        
        // Zavolame funkci, ktera sestavi dotaz
        $sql = $this->buildQuery();
        
        // Zavolame vykonani dotazu
        $result = $this->executeQuery($sql, $limit, $offset, 'id');
        
        // Vracime vysledek
        return $result;        
    }
    
    
    /**
     * Nalezne vsechny zamestnance pro danou pobocku.
     * 
     * @param int $id
     * @param array $sorter
     * @param int $limit
     * @param int $offset
     * @return array()
     */
    public function findEmployees($id, array $sorter = array())
    {
        // Zaklad dotazu
        $sql = "SELECT `e`.`id`"
                ." FROM `rentalOffice` AS `ro` JOIN `employee` AS `e` ON (`ro`.`id` = `e`.`rentalOffice_id`)"
                ." WHERE `ro`.`id` = '$id'";
        
        // Pridame razeni
        if (!empty($sorter)) {
            $sql .= ' ORDER BY ' . join(', ', $sorter);
        }
          
        // Provedeme dotaz a ziskame data
        $res = $this->dbconn->query($sql);
        if ($res->numRows > 0) {
            
            // Vytvorime entity
            $entities = array();
            while (($row = $this->dbconn->fetch($res->result))) {
                $entities[] = $this->loader->get('CC_Employee', $row->id);
            }
            
            return $entities;
        }
        
        return array();        
    }
    
    
}
