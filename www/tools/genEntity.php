<?php

// Nastaveni konstant pro pripojeni k DB
define("SQL_HOST", "localhost");
define("SQL_DBNAME", "ccccar");
define("SQL_USERNAME", "ccccar");
define("SQL_PASSWORD", "xxx");

// Projeni k DB
$db = mysql_connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD) or die ("Nepodařilo se připojit k MySQL");
mysql_select_db(SQL_DBNAME) or die ("Nepodařilo se připojit k databázi");
mysql_query("SET NAMES UTF8");



/** ************************************************************************* */
/**                       POKUD BYLO ODESLANO                                 */ 
/** ************************************************************************* */

if(!empty($_POST))
{
	$generate = $_POST["generate"];
	$entity = $_POST["entity_name"];
	$visibility = $_POST["visibility"];
	$flags = array();
	$flags['prologue'] = (isset($_POST["prologue"])) ? true : false;
	$flags['epilogue'] = (isset($_POST["epilogue"])) ? true : false;
	
	$flags['attributes'] = (isset($_POST["attributes"])) ? true : false;
	$flags['construct'] = (isset($_POST["construct"])) ? true : false;
	$flags['getters'] = (isset($_POST["getters"])) ? true : false;
	$flags['setters'] = (isset($_POST["setters"])) ? true : false;
	$flags['comments'] = (isset($_POST["comments"])) ? true : false;
	
	$tables = (isset($_POST["table"])) ? $_POST["table"] : NULL;
	
	if($tables == NULL) {
		echo "<h1>No tables selected!!!</h1>";
		die;
	}
	
	foreach($tables as $table) {
		$columns[] = array('table' => $table);
		
		$result = mysql_query("show columns from `" . $table ."`", $db);
		while ($column = mysql_fetch_array($result)) {
			$col = array();
			$col['name'] = $column[0];
			$col['type'] = getVarType($column[1]);
			$col['default'] = getDefaultValue($column[1], $column[4]);
			
			$columns[] = $col;
		}
	}
	
	
	
	/** VAR NAMES **/

	$columns = prepareVarNames($columns);
	$columns = prepareGetterNames($columns);
	$columns = prepareSetterNames($columns);
	
	
	/** RENDERING **/
	if($generate == "a2e") {
		echo "<h1>arrayToEntity</h1>";
		echo "<pre>";
		renderArrayToEntity($columns, $entity);
		echo "</pre>";
	}
	else if($generate == "e2a") {
		echo "<h1>entityToArray</h1>";
		echo "<pre>";
		renderEntityToArray($columns, $entity);
		echo "</pre>";
	}
	else {
		echo "<pre>";
		if($flags['prologue'])renderPrologue($entity);
		if($flags['attributes']) renderAttributes($columns, $visibility, $flags['comments']);
		if($flags['construct']) renderConstruct($entity, $flags['comments']);
		if($flags['getters']) renderGetters($columns, $flags['comments']);
		if($flags['setters']) renderSetters($columns, $entity, $flags['comments']);
		if($flags['epilogue'])renderEpilogue($entity);
		echo "</pre>";
	}
}

/** ************************************************************************* */
/**                      POKUD NEBYLO ODESLANO                                */ 
/** ************************************************************************* */

else
{
	$tables = array();
	$result = mysql_query("show tables", $db);
	while ($table = mysql_fetch_array($result)) {
		$tables[] = $table[0];
	} 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-2" />
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="pragma" content="no-cache" />
	<title>CCC Car - Entity Generator</title>

	<style type="text/css">
		body {margin:0; padding:0; background:#FFF; font-family:Verdana, Arial, sans-serif;
			font-size: 12px;color:#000}
		#container {margin:50px auto; padding:0; background:#FFF; width: 90%;color:#808080}

		a         {color:#003365; text-decoration:underline}
		a:hover   {color:#7F3F3C; text-decoration:none}
		h1        {font-size:140%;color:#000;}
		h2        {margin:20px 0 0 0;font-size:120%;color:#000}
		h3        {margin:0;}
		p         {font-size:90%}
		table     {margin:10px auto; width:100%; height:auto; border-collapse:collapse; border:1px #808080 solid;color:#000}
		tr        {height:20px;background:#FFF8DC;}
		th        {padding:4px 6px; border:1px #808080 solid;}
		td        {padding:4px 6px; border:1px #808080 solid;}
		.head     {height:40px;background:#ADD8E6;color:#003365}
		.parent   {background:#F0F8F0}
		.even     {background:#DFE7CF}
		.center   {text-align:center;}
		.bold     {font-weight:bold;}

		fieldset  {margin:10px auto;padding:20px 20px; border:1px #808080 solid;background:#F0F8F0;color:#000}
		legend    {font-weight:bold;}
		label     {margin:0 20px 0 0; cursor: pointer;}
		form      {margin:0 auto 20px auto;}

		.report_ko  {color: #F00; border: 1px #F00 solid; background: #FFDEAD}
		.report_ok  {color: #003365; border: 1px #003365 solid; background: #ADD8E6}

		.footer   {margin:20px auto; padding:20px 0; border-top:1px #808080 solid;text-align:center}
	</style>

</head>
<body>  
	<div id="container">  
	<form action="genEntity.php" method="post">
		
		<fieldset>
			<legend>&nbsp;What you want?&nbsp;</legend>
			<table>
				<tr>
					<td>Generate:</td>
					<td>
						<input type="radio" id="gen_ent" name="generate" value="ent" checked="checked" /> <label for="gen_ent">Entity</label>
						<input type="radio" id="gen_a2e" name="generate" value="a2e" /> <label for="gen_a2e">arrayToEntity</label>
						<input type="radio" id="gen_e2a" name="generate" value="e2a" /> <label for="gen_e2a">entityToArray</label>
					</td>
				</tr>
			</table>
		</fieldset>
	
	
		<fieldset>
			<legend>&nbsp;Settings&nbsp;</legend>
			<table>
				<tr>
					<td><label for="entity_name">Entity Name: </label></td>
					<td><input type="text" id="entity_name" name="entity_name" value="" /> Entity</td>
				</tr>
				<tr>
					<td>Visibility:</td>
					<td>
						<input type="radio" id="vis_public" name="visibility" value="public" checked="checked" /> <label for="vis_public">public</label>
						<input type="radio" id="vis_protected" name="visibility" value="protected" /> <label for="vis_protected">protected</label>
						<input type="radio" id="vis_private" name="visibility" value="private" /> <label for="vis_private">private</label>
					</td>
				</tr>
				<tr>
					<td>Others:</td>
					<td>
						<input type="checkbox" id="prologue" name="prologue" value="1" checked="checked" /> <label for="prologue">prologue</label><br />
						<input type="checkbox" id="epilogue" name="epilogue" value="1" checked="checked" /> <label for="epilogue">epilogue</label><br /><br />
						
						<input type="checkbox" id="attributes" name="attributes" value="1" checked="checked" /> <label for="attributes">attributes</label><br />
						<input type="checkbox" id="construct" name="construct" value="1" /> <label for="construct">construct</label><br />
						<input type="checkbox" id="getters" name="getters" value="1" checked="checked" /> <label for="getters">getters</label><br />
						<input type="checkbox" id="setters" name="setters" value="1" checked="checked" /> <label for="setters">setters</label><br />
						<input type="checkbox" id="comments" name="comments" value="1" checked="checked" /> <label for="comments">comments</label><br />
					</td>
				</tr>
			</table>
		</fieldset>
		
		<fieldset>
			<legend>&nbsp;Select tables&nbsp;</legend>
			<table>
<?php
	$count = count($tables);
	$cols = 4;
	$rows = $count / $cols;
	$index = 0;
	
	for($r = 0; $r < $rows; $r++)
	{
?><tr><?php
		
		for($c = 0; $c < $cols; $c++) 
		{
?><td><?php
			
			if(isset($tables[$index])) {
?>
				<input type="checkbox" id="table_<?php echo $tables[$index]; ?>" name="table[]" value="<?php echo $tables[$index]; ?>" />
				<label for="table_<?php echo $tables[$index]; ?>"><?php echo $tables[$index]; ?></label>
<?php
			}
			else {
				echo "&nbsp;";
			}
			
			
?></td><?php
			$index++;
		}
?></tr><?php
	}
?>				
			</table>
		</fieldset>
		
		<fieldset>
			<legend>&nbsp;Generate&nbsp;</legend>
			<table>
				<tr>
					<td>
						<input type="submit" value="Generate !!!" />
						<input type="reset" value="Reset" />
					</td>
				</tr>
			</table>
		</fieldset>

	</form>
	</div>
</body>
</html>
 
<?php	
}








/***
* FUNCTIONS
*/


function renderArrayToEntity($columns, $entity)
{
	echo "        // Naplni entitu daty<br />";
	
	foreach($columns as $column) {
		if(isset($column['table'])) {
			echo "<br /><br />        /** ************************* ".$column['table']." *************************** */<br />";
			continue;
		}
		echo "        if(isset(\$row['".$column['name']."'])) \$entity->".$column['var']." = \$row['".$column['name']."'];<br />";
	}
	echo "        <br />";
}


function renderEntityToArray($columns, $entity)
{
	echo "        // Naplni pole daty<br />";
	
	foreach($columns as $column) {
		if(isset($column['table'])) {
			echo "<br /><br />        /** ************************* ".$column['table']." *************************** */<br />";
			continue;
		}
		echo "        if(\$entity->hasChanged('".$column['name']."')) \$row['".$column['name']."'] = \$entity->".$column['var'].";<br />";
	}
	echo "        <br />";	
}



/*** ********************************************************************** ***/


/** RENDER PROLOGUE **/
function renderPrologue($entity)
{
    echo "/**<br />";
    echo " * CC_$entity.php<br />";
    echo " *<br />";
    echo " * Entita reprezentujici ... . Odpovida tabulce '".lcfirst($entity)."'.<br />";
    echo " *<br />";
    echo " * @copyright 2014<br />";
    echo " * @version 1.0.0<br />";
    echo " */<br /><br />";


    echo "final class CC_$entity extends CC_Entity<br />";
    echo "{<br /><br />";
}


/** RENDER EPILOGUE **/
function renderEpilogue($entity)
{
	echo "}<br /><br />";
}



/** GENETERATE ATTRIBUTES **/
function renderAttributes($columns, $visibility, $comments)
{
	if($comments) {
		//echo "    /** ********************************************************************* */<br />";
		//echo "    /**                           TRIDNI ATRIBUTY                             */<br />";
		//echo "    /** ********************************************************************* */<br /><br /><br />";
	}
	
	foreach($columns as $column) {
		if(isset($column['table']) || (isset($column['name']) && $column['name'] == 'id')) {
			continue;
		}
		
		if($comments) {
			echo "    /** @var ".$column['type']." */<br />";
		}
		echo "    ". $visibility . " \$" . $column['var'] . " = " . $column['default'] . ";<br /><br /><br />";
	}   
}


/** GENERATE CONSTUCT */
function renderConstruct($entity, $comments)
{
    if($comments) {
		echo "    /** ********************************************************************* */<br />";
		echo "    /** Rozhrani CC_Entity                                                    */<br />";
		echo "    /** ********************************************************************* */<br /><br /><br />";
	}
    
    
    if($comments) {
        echo "    /**<br />";
        echo "     * Nacte entitu.<br />";
        echo "     * <br />";
        echo "     * @see CC_Entity::load()<br />";
        echo "     * @return boolean<br />";
        echo "     */<br />";
    }
    echo "    public function load(\$filter = array())<br />";
    echo "    {<br />";
    echo "        // Test, zda je ID platne<br />";
    echo "        if (\$this->id <= 0) {<br />";
    echo "            return false;<br />";
    echo "        }<br />";
    echo "        <br />";
    echo "        // Ziskame zaznam z DB<br />";
    echo "        \$sql = \"SELECT * FROM `".lcfirst($entity)."` WHERE `id` = '\" . \$this->id . \"'\";<br />";
    echo "        \$res = \$this->dbconn->query(\$sql);<br />";
    echo "        <br />";
    echo "        // Test, zda byl skutecne vracen pouze 1 radek<br />";
    echo "        if (\$res->numRows == 1) {<br />";
    echo "            \$row = \$this->dbconn->fetch(\$res->result);<br />";
    echo "                <br />";
    echo "            // Naplni instanci daty z DB<br />";
    echo "            // TODO: ...<br />";
    echo "            <br />";
    echo "            <br />";
    echo "            return true;<br />";
    echo "        }<br />";
    echo "        <br />";
    echo "        return false;<br />";
    echo "    }<br /><br /><br />";
    
    
    if($comments) {
        echo "    /**<br />";
        echo "     * Vytvori novou entitu v DB.<br />";
        echo "     * <br />";
        echo "     * @see CC_Entity::create()<br />";
        echo "     * @return boolean<br />";
        echo "     */<br />";
    }
    echo "    protected function create()<br />";
    echo "    {<br />";
    echo "        // Vlozime novy zaznam do DB<br />";
    echo "        \$sql = \"INSERT INTO `".lcfirst($entity)."` (...) VALUES (...)\";<br />";
    echo "        \$res = \$this->dbconn->query(\$sql);<br />";
    echo "        <br />";
    echo "        // Test, zda bylo neco vlozeno<br />";
    echo "        if (\$res->affectedRows == 1) {<br />";
    echo "            // Nastavime nove ID uzivateli<br />";
    echo "            \$this->id = (int)\$res->lastId;<br />";
    echo "            return true;<br />";
    echo "        }<br />";
    echo "        <br />";
    echo "        return false;<br />";
    echo "    }<br /><br /><br />";
    
    
    if($comments) {
        echo "    /**<br />";
        echo "     * Aktualizuje stavajici entitu v DB.<br />";
        echo "     * <br />";
        echo "     * @see CC_Entity::update()<br />";
        echo "     * @return boolean<br />";
        echo "     */<br />";
    }
    echo "    protected function update()<br />";
    echo "    {<br />";
    echo "        // Test, zda je ID platne<br />";
    echo "        if (\$this->id <= 0) {<br />";
    echo "            return false;<br />";
    echo "        }<br />";
    echo "        <br />";
    echo "        // Vlozime novy zaznam do DB<br />";
    echo "        \$sql = \"UPDATE `".lcfirst($entity)."` SET ... WHERE `id` = '\".\$this->id.\"'\";<br />";
    echo "        \$res = \$this->dbconn->query(\$sql);<br />";
    echo "        <br />";
    echo "        // Test, zda bylo neco vlozeno<br />";
    echo "        if (\$res->result && !\$res->error) {<br />";
    echo "            return true;<br />";
    echo "        }<br />";
    echo "        <br />";
    echo "        return false;<br />";
    echo "    }<br /><br /><br />";
    
    
    if($comments) {
        echo "    /**<br />";
        echo "     * Smaze entitu z DB.<br />";
        echo "     * <br />";
        echo "     * @see CC_Entity::delete()<br />";
        echo "     * @return boolean<br />";
        echo "     */<br />";
    }
    echo "    protected function delete()<br />";
    echo "    {<br />";
    echo "        // Test, zda je ID platne<br />";
    echo "        if (\$this->id <= 0) {<br />";
    echo "            return false;<br />";
    echo "        }<br />"; 
    echo "        <br />";
    echo "        // Vlozime novy zaznam do DB<br />";
    echo "        \$sql = \"DELETE FROM `".lcfirst($entity)."` WHERE `id` = '\".\$this->id.\"'\";<br />";
    echo "        \$res = \$this->dbconn->query(\$sql);<br />";
    echo "        <br />";
    echo "        // Test, zda bylo neco vlozeno<br />";
    echo "        if (\$res->result && !\$res->error) {<br />";
    echo "            // Entita smazana, nyni je nutne ji odebrat z modelLoaderu!<br />";
    echo "            \$this->loader->detach(\$this);<br />";
    echo "            \$this->id = null;<br />";
    echo "            return true;<br />";
    echo "        }<br />";
    echo "       <br />";
    echo "       return false;<br />";
    echo "    }<br /><br /><br />";
    
}


/** GENETERATE GETTERS **/
function renderGetters($columns, $comments)
{
	if($comments) {
		echo "    /** ********************************************************************* */<br />";
		echo "    /** Gettery                                                               */<br />";
		echo "    /** ********************************************************************* */<br /><br /><br />";
	}
	
	foreach($columns as $column) {
		if(isset($column['table']) || (isset($column['name']) && $column['name'] == 'id')) {
			continue;
		}
		
		
		if($comments) {
			echo "    /**<br />";
			echo "     * Vraci ...<br />";
			echo "     * <br />";
			echo "     * @return ".$column['type']."<br />";
			echo "     */<br />";
		}
		
		echo "    public function " . $column['getter'] . "()<br />";
		echo "    {<br />";
		echo "        return \$this->" . $column['var'] . ";<br />";
		echo "    }<br /><br /><br />"; 
	}   
}


/** GENETERATE SETTERS **/
function renderSetters($columns, $entity, $comments)
{
	if($comments) {
		echo "    /** ********************************************************************* */<br />";
		echo "    /** Settery                                                               */<br />";
		echo "    /** ********************************************************************* */<br /><br /><br />";
	}
	
	foreach($columns as $column) {
		if(isset($column['table']) || (isset($column['name']) && $column['name'] == 'id')) {
			continue;
		}
		
		
		if($comments) {
			echo "    /**<br />";
			echo "     * Nastavi ...<br />";
            echo "     * <br />";
			echo "     * @param ".$column['type']." $" . $column['var'] ."<br />";
			echo "     * @return CC_$entity<br />";
			echo "     */<br />";
		}
		echo "    public function " . $column['setter'] . "(\$".$column['var'].")<br />";
		echo "    {<br />";
		echo "        \$this->" . $column['var'] . " = (".$column['type'].")\$".$column['var'].";<br />";
		echo "        return \$this;<br />";
		echo "    }<br /><br /><br />"; 
	}  
}


function prepareVarNames($columns)
{
	$cols = array();
	foreach($columns as $column) {
		if(!isset($column['table'])) {
			$varParts = explode('_', $column['name']);
			
			if(count($varParts)) {
                $varParts = array_map('ucfirst', $varParts);
			}
			
			$column['var'] = lcfirst(join('', $varParts));
		}
		
		$cols[] = $column;
	}
	return $cols;
}


function prepareGetterNames($columns)
{
	$cols = array();
	foreach($columns as $column) {
		if(!isset($column['table'])) {
			$varName = ucfirst($column['var']);
			
			if($column['type'] === 'bool') {
				$varName = 'is'.$varName;
			}
			else {
				$varName = 'get'.$varName;
			}
			
			$column['getter'] = $varName;
		}
				
		$cols[] = $column;
	}
	return $cols;
}


function prepareSetterNames($columns)
{
	$cols = array();
	foreach($columns as $column) {
		if(!isset($column['table'])) {
			$varName = ucfirst($column['var']);
			$varName = 'set'.$varName;
			
			$column['setter'] = $varName;
		}        
		$cols[] = $column;
	}
	return $cols;
}


function getVarType($type) {
	if(preg_match('#^int(.*)$#', $type)) {
		return 'int';
	}
	else if(preg_match('#^varchar(.*)$#', $type) || preg_match('#^text(.*)$#', $type) || preg_match('#^char(.*)$#', $type)) {
		return 'string';
	}
	else if(preg_match('#^datetime(.*)$#', $type) || preg_match('#^time(.*)$#', $type) || preg_match('#^date(.*)$#', $type)) {
		return 'DateTime';
	}
	else if(preg_match('#^tinyint(.*)$#', $type)) {
		return 'bool';
	}
	else if(preg_match('#^decimal(.*)$#', $type)) {
		return 'float';
	}
	
	return '';    
}


function getDefaultValue($type, $default) {
	if(preg_match('#^tinyint(1)$#', $type)) {
		return ($default) ? 'true' : 'false';
	}
	else if(preg_match('#^int(.*)$#', $type) || preg_match('#^tinyint(.*)$#', $type)) {
		return (is_numeric($default)) ? $default : 'NULL';
	}
	else if(preg_match('#^varchar(.*)$#', $type) || preg_match('#^text(.*)$#', $type) || preg_match('#^char(.*)$#', $type)) {
		return 'NULL';
	}
	else if(preg_match('#^datetime(.*)$#', $type) || preg_match('#^time(.*)$#', $type) || preg_match('#^date(.*)$#', $type)) {
		return 'NULL';
	}
	else if(preg_match('#^decimal(.*)$#', $type)) {
		return (is_float($default)) ? $default : '0.0';
	}
}