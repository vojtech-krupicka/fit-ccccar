<?php
/**
 * CC_VehicleList.php
 *
 * Kolekce reprezentujici seznam entit typu vozidlo.
 *
 * @copyright 2014
 * @version 1.0.0
 */


final class CC_VehicleList extends CC_EntityList
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
        $this->selectClause[] = "`v`.`id`";
        $this->fromClause[] = "`vehicle` AS `v`";
        
        // Filtrujeme podle ID pobocky
        if (isset($filter['rentalOffice_id'])) {
            $this->whereClause[] = "`v`.`rentalOffice_id` = '" . (int)$filter['rentalOffice_id'] . "'";
        }
        // Filtrujeme podle ID typu paliva
        if (isset($filter['vehicleFuelType_id'])) {
            $this->whereClause[] = "`v`.`vehicleFuelType_id` = '" . (int)$filter['vehicleFuelType_id'] . "'";
        }
        // Filtrujeme podle ID barvy
        if (isset($filter['vehicleColor_id'])) {
            $this->whereClause[] = "`v`.`vehicleColor_id` = '" . (int)$filter['vehicleColor_id'] . "'";
        }
        // Filtrujeme podle ceny na den
        if (isset($filter['costPerDay_from'])) {
            $this->whereClause[] = "`v`.`costPerDay` >= '" . (float)$filter['costPerDay_from'] . "'";
        }
        if (isset($filter['costPerDay_to'])) {
            $this->whereClause[] = "`v`.`costPerDay` <= '" . (float)$filter['costPerDay_to'] . "'";
        }
        // Filtrujeme podle ceny na km
        if (isset($filter['costPerUnit_from'])) {
            $this->whereClause[] = "`v`.`costPerUnit` >= '" . (float)$filter['costPerUnit_from'] . "'";
        }
        if (isset($filter['costPerUnit_to'])) {
            $this->whereClause[] = "`v`.`costPerUnit` <= '" . (float)$filter['costPerUnit_to'] . "'";
        }
        
        // Radime
        if (isset($sorter['vehicle_name'])) {
            $this->fromClause[] = "`vehicleType` AS `vt`";
            $this->whereClause[] = "`v`.`vehicleType_id` = `vt`.`id`";
            $this->sortClause[] = '`vt`.`manufacturer` ' . $sorter['vehicle_name'];
            $this->sortClause[] = '`vt`.`brand` ' . $sorter['vehicle_name'];
        } else if (isset($sorter['cost_per_day'])) {
            $this->sortClause[] = '`v`.`costPerDay` ' . $sorter['cost_per_day'];
        } else if (isset($sorter['cost_per_km'])) {
            $this->sortClause[] = '`v`.`costPerUnit` ' . $sorter['cost_per_km'];
        }
        
        
        // Zavolame funkci, ktera sestavi dotaz
        $sql = $this->buildQuery();
                        
        // Zavolame vykonani dotazu
        $result = $this->executeQuery($sql, $limit, $offset, 'id');
        
        // Vracime vysledek
        return $result;        
    }
}
