<?php

/*
 * genTables.php - dev helper.
 * Connects to the database and prints a PHP `const` line per table
 * (uppercased, with any `hl_` prefix stripped). Dev use only.
 */

$tables = array();                
$conts = array();

define("SQL_HOST","localhost");
define("SQL_DBNAME","ccccar");
define("SQL_USERNAME","ccccar");
define("SQL_PASSWORD","xxx");

$db = mysql_connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD) or die ("Nepodařilo se připojit k MySQL");
mysql_select_db(SQL_DBNAME) or die ("Nepodařilo se připojit k databázi");
mysql_query("SET NAMES UTF8"); 

$result = mysql_query("show tables", $db);
while ($table = mysql_fetch_array($result)) {
	$tables[] = $table[0];
} 

foreach($tables as $table) {
	$table = preg_replace('#^hl_(.*)$#', '$1', $table);
	$conts[] = strtoupper($table);
}                
				
echo "<pre>";
for($i = 0; $i < count($tables); $i++) {
	echo "const " . $conts[$i] . " = '". $tables[$i] . "';<br />";
}   
echo "</pre>";             