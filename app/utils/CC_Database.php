<?php
/**
 * CC_Database.php
 *
 * Pomocna trida pro praci s databazi
 *
 * @copyright 2014
 * @version 1.0.0
 */


final class CC_Database
{
    /** @var string */
    private $dbconn = null;
    
    
    /**
     * Konstruktor tridy CC_Database
     */
    public function __construct()
    {
        
    }
    
    
    /**
     * Pripoji se k DB.
     * 
     * @param string $server Jmeno DB serveru
     * @param string $database Jmeno databaze
     * @param string $username Jmeno uzivatele
     * @param string $password Heslo uzivatele
     * @param string $charset Kodovani v jakem jsou ulozena data v DB
     * @return boolean Vraci true, pokud pripojeni projeni probehlo v poradku, jinak false.
     */
    public function connect($server, $database, $username, $password, $charset = 'utf8')
    {
        // Pripojeni k databazovemu serveru
        $link = mysql_connect($server, $username, $password);
    
        if(!$link) {
          dump(mysql_error());
          return false;
        }
        
        // Vyber databaze, se kterou se bude pracovat
        $db = mysql_select_db($database, $link);

        if(!$db) {
          dump(mysql_error($link));
          return false;
        }
        
        // Nastavi kodovani a ulozi pripojeni do tridni promenne
        mysql_query("SET CHARACTER SET " . $charset, $link);
        $this->dbconn = $link;
        
        return true;
    }
    
    
    /**
     * Funkce pro odpojeni od databazoveho serveru.
     */
    public function disconnect()
    {
        mysql_close($this->dbconn);
    } 
    
    
    /**
     * Nastartuje transakci.
     * 
     * @return boolean Vraci true, pokud se podarilo transakci uspesne nastartovat, jinak false.
     */
    public function begin()
    {
        $sql = "SET autocommit = 0; START TRANSACTION;";
        $this->query($sql);

        return true;
    }


    /**
     * Potvrdi transakci.
     * 
     * @return boolean Vracti true, pokud se podarilo transakci uspesne potvrdit, jinak false.
     */
    public function commit()
    {
        // Ukonceni transakce
        $sql = "COMMIT; SET autocommit = 1;";
        $this->query($sql);

        return true;
    }

    
    /**
     * Zrusi transakci.
     * 
     * @return boolean Vraci true, pokud se podarilo transakci uspesne zrusit, jinak false.
     */
    public function rollback()
    {
        // Zruseni provedenych zmen, pokud doslo behem ulozeni dokladu k chybe
        $sql = "ROLLBACK; SET autocommit = 1;";
        $this->query($sql);

        return true;
    }
    
    
    /**
    * Nastaveni ukazatele ve vysledku SQL dotazu na zadany radek.
     * 
    * @param resource $resultSet Vysledek provedeni SQL dotazu.
    * @param int $row Cislo radku podle poradi ve vysledku SQL dotazu.
    * @return boolena TRUE pokud nastaveni ukazatele probehlo uspesne, jinak FALSE.
    */
    public function seek($resultSet, $row)
    {
        return mysql_data_seek($resultSet, $row);
    }
    
    
    /**
     * Funkce pro provedeni databazoveho prikazu.
     * 
     * @param string $sql SQL dotaz, ktery ma byt proveden.
     * @return stdClass Vysledku dotazu:
     *  - affectedRows Pocet ovlivnenych radku dotazem
     *  - numRows Pocet ziskanych radku dotazem
     *  - lastId Naposledy vlozena hodnota autoincrementu
     *  - result ResultSet dotazu
     *  - error Priznak, zda doslo k chybe pri vykonavani dotazu
     *  - errorMsg Originalni MySQL zprava chyby
     */
    public function query($sql)
    {
        $resultSet = new stdClass;
        $resultSet->affectedRows = 0;
        $resultSet->numRows = 0;
        $resultSet->lastId = 0;
        $resultSet->result = null;
        $resultSet->error = false;
        $resultSet->errorMsg = null;
        
        // Provede dotazu
        $result = mysql_query($sql, $this->dbconn);
        
        // Pokud byl dotaz proveden v poradku
        if($result) {
            // Ulozi vysledny resultset
            $resultSet->result = $result;
            
            // Prevede dotaz na mala pismena (kvuli naslednemu porovnani)
            $lowerSql = strtolower($sql);
            
            // Ziska pocet radku vracenych dotazem - pouze pokud se jedna o SELECT, nebo SHOW
            if (substr($lowerSql, 0, 6) == 'select' || substr($lowerSql, 0, 6) == 'show') {
                $resultSet->numRows = mysql_num_rows($result);
            } else {
                // Jinak zista pocet ovlivnenych radku
                $resultSet->affectedRows = mysql_affected_rows($this->dbconn);
                
                // Pokud slo navic o INSERT, ziska i ID posledniho vlozeneho radku
                if (substr($lowerSql, 0, 6) == 'insert') {
                    $resultSet->lastId = mysql_insert_id($this->dbconn);
                }
            }
        } else {
            dump(mysql_error($this->dbconn));
            $resultSet->error = true;
            $resultSet->errorMsg = mysql_error($this->dbconn);
        }
        
        return $resultSet;
    }
    
    
    /**
     * Z resultsetu dotazu ziska jeden radek jako stdClass.
     * 
     * @param resource $resultSet Vysledek provedeni SQL dotazu.
     * @return stdClass|NULL Radek nebo false pri chybe.
     */
    public function fetch($resultSet)
    {
        // Ziska jeden radek z resultsetu
        $rawRow = mysql_fetch_array($resultSet);
        
        // Radek je platny (nejsme na konci dat)
        if ($rawRow) {
            $row = new stdClass;
        
            // Radek projde (po sloupcich - polozky v poly) a naplni jim stdClass $row
            foreach ($rawRow as $key => $data) {
                $row->$key = $data;
            }

            // Vraci ziskany radek
            return $row;
        }
        
        return null;        
    }
    
    
    /**
     * Z resultsetu ziska vsechny zaznamy jako stdClass, ktere vraci v poli 
     * tak jak jsou za sebou.
     * 
     * @param resource $resultSet Vysledek provedeni SQL dotazu.
     * @return stdClass|NULL Radek nebo false pri chybe.
     */
    public function fetchAll($resultSet)
    {
        $result = array();
        while(($row = $this->fetch($resultSet)) != null) {
            $result[] = $row;
        }
        
        return $result;
    }
    
    
    /**
     * Z resultsetu ziska vsechny zaznamy jako stdClass, ktere vraci v mape,
     * kde klicem je $key.
     * 
     * @param resource $resultSet Vysledek provedeni SQL dotazu.
     * @return stdClass|NULL Radek nebo false pri chybe.
     */
    public function fetchAssoc($resultSet, $key = 'id')
    {
        $result = array();
        while(($row = $this->fetch($resultSet)) != null) {
            $result[$row->$key] = $row;
        }
        
        return $result;
    }
    
}