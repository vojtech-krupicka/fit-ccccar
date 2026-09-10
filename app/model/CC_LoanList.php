<?php
/**
 * CC_LoanList.php
 *
 * Kolekce reprezentujici seznam entit typu vypujcku vozidla.
 *
 * @copyright 2014
 * @version 1.0.0
 */


final class CC_LoanList extends CC_EntityList
{
    
    /**
     * Nalezne vsechny vypujcky.
     * 
     * @see CC_EntityList::find()
     * @param array $filter
     * @param array $sorter
     * @param type $limit
     * @param type $offset
     * @return array()
     */
    public function find(array $filter = array(), array $sorter = array(), $limit = null, $offset = null)
    {
        // Definujeme jednotlive clausule
        $this->selectClause[] = "`l`.`id`";
        $this->fromClause[] = "`loan` AS `l`";
        
        // Filtrujeme podle ID vypujcky
        if (isset($filter['loan_id'])) {
            $this->whereClause[] = "`l`.`id` = " . (int)$filter['loan_id'];
        }
        
        // Filtrujeme podle ID zakaznika
        if (isset($filter['customer_id'])) {
            $this->whereClause[] = "`l`.`customer_id` = " . (int)$filter['customer_id'];
        }
        // Filtrujeme podle ID vozidla
        if (isset($filter['vehicle_id'])) {
            $this->whereClause[] = "`l`.`vehicle_id` = " . (int)$filter['vehicle_id'];
        }
        
        // Filtrujeme pouze aktivni vypujcky
        if (isset($filter['active_loans'])) {
            $this->whereClause[] = "((`startAt` IS NOT NULL AND `endAt` IS NULL) OR (`startAt` IS NULL AND `deleted` = '0'))";
        }        
        // Filtrujeme podle zacatku rezervace
        if (isset($filter['reservedFrom'])) {
            $this->whereClause[] = "`l`.`reservedFrom` >= " . $filter['reservedTo']->format('Y-m-d');
        }
        // Filtrujeme podle konce rezervace
        if (isset($filter['reservedTo'])) {
            $this->whereClause[] = "`l`.`reservedTo` <= " . $filter['reservedTo']->format('Y-m-d');
        }
        
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
