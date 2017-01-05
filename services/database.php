<?

// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class database {
	
	//************************************************************************************
	function __construct() {
	}

	//************************************************************************************
	function __destruct() {
	}

	//************************************************************************************
	protected function OpenDbConnection()
	{
		self::logEntry(__CLASS__, __FUNCTION__);

		$result = null;

		try
		{
			if(strpos($_SERVER['HTTP_HOST'], 'localhost') > -1 )
			{
				$user = 'root';
				$password = 'root';
				$db = 'chat';
				$host = '127.0.0.1';
				$port = 3306;
				$socket = 'localhost:/Applications/MAMP/tmp/mysql/mysql.sock';

				$link = mysqli_init();
				$this->dblink = mysqli_connect(
				   $host,
				   $user, 
				   $password, 
				   $db,
				   $port,
				   $socket
				);
			}
			else
			{
				$this->dbserver = DB_SERVER;
				$this->dbname = DB_NAME;
				$this->dbuser = DB_USER;
				$this->dbpwd = DB_PASSWORD;
				if(isset($this->dblink)) $this->closeDbConnection();
				$this->dblink = mysqli_connect($this->dbserver, $this->dbuser, $this->dbpwd, $this->dbname);
				$this->dblink->query("SET CHARACTER SET utf8");
			}
	
			if(!$this->dblink)
			{
				throw new exception("Database error [" . mysqli_connect_errno() . "]: " . mysqli_connect_error() . "\n");
			}
		}
		catch(exception $e)
		{
			$systemMessage = "ERROR! " . __FILE__ . "\n\t"
				. __CLASS__. "::" . __FUNCTION__ . "(): " . $e->getMessage();
			$this->recordError($systemMessage);
			$result = $this->getJsonError("Internal server error experienced while opening database connection.");
		}

		self::logExit(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	//************************************************************************************
	protected function startTransaction()
	{
		self::logEntry(__CLASS__, __FUNCTION__);

		$result = null;

		try
		{
			$mysqli = $this->dblink;
			$query = "START TRANSACTION";
			$sqlResult = $mysqli->query($query);
			if (!$sqlResult) {
				throw new exception("Database error [" . $mysqli->errno . "]: "
					. $mysqli->error . " at line " . __LINE__ . "\n" . "Query: " . $query);
			}
		}
		catch(exception $e)
		{
			$systemMessage = "ERROR! " . __FILE__ . "\n\t"
				. __CLASS__. "::" . __FUNCTION__ . "(): " . $e->getMessage();
			$this->recordError($systemMessage);$result = null;
			$result = $this->getJsonError("Internal server error experienced while starting database transaction.");
		}

		self::logExit(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	//************************************************************************************
	protected function endTransaction()
	{
		self::logEntry(__CLASS__, __FUNCTION__);

		$result = null;

		try
		{
			$mysqli = $this->dblink;
			$query = "COMMIT WORK";
						
			$sqlResult = $mysqli->query($query);
			if (!$sqlResult) {
				throw new exception("Database error [" . $mysqli->errno . "]: "
					. $mysqli->error . " at line " . __LINE__ . "\n" . "Query: " . $query);
			}
		}
		catch(exception $e)
		{
			$systemMessage = "ERROR! " . __FILE__ . "\n\t"
				. __CLASS__. "::" . __FUNCTION__ . "(): " . $e->getMessage();
			$this->recordError($systemMessage);
			$result = $this->getJsonError("Internal server error experienced while ending database transaction.");
		}

		self::logExit(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	//************************************************************************************
	protected function rollbackTransaction()
	{
		self::logEntry(__CLASS__, __FUNCTION__);

		$result = null;

		try
		{
			$mysqli = $this->dblink;
			$query = "ROLLBACK WORK";
						
			$sqlResult = $mysqli->query($query);
			if (!$sqlResult) {
				throw new exception("Database error [" . $mysqli->errno . "]: "
					. $mysqli->error . " at line " . __LINE__ . "\n" . "Query: " . $query);
			}
		}
		catch(exception $e)
		{
			$systemMessage = "ERROR! " . __FILE__ . "\n\t"
				. __CLASS__. "::" . __FUNCTION__ . "(): " . $e->getMessage();
			$this->recordError($systemMessage);
			$result = $this->getJsonError("Internal server error experienced while rolling back database transaction.");
		}

		self::logExit(__CLASS__, __FUNCTION__, $result);

		return $result;
	}
	
	//************************************************************************************
	protected function getColumns($tableName, $mysqli, $requiredOnly)
	{
		self::logEntry(__CLASS__, __FUNCTION__);

		$result = null;

		try
		{
			if (!isset($mysqli)) {
				throw new exception("Database Link Object Undefined!");
			}

			$query = "SHOW COLUMNS FROM " . $tableName . " ";

			$sqlResult = $mysqli->query($query);
			if (!isset($sqlResult)) {
				throw new exception("Database error [" . $mysqli->errno . "]: "
					. $mysqli->error . " at line " . __LINE__ . "\n" . "Query: " . $query);
			}
			
			$result = array();
			while ($row = $sqlResult->fetch_assoc()) {
				$requiredField = true;
				if($requiredOnly)
				{
					$requiredField = (strtolower($row["Null"]) == "no");					
				}
				if(!$requiredOnly || $requiredField)
					$result[sizeof($result)] = $row["Field"];
			}			
			
			$sqlResult->free();
		}
		catch(exception $e)
		{
			$systemMessage = "ERROR! " . __FILE__ . "\n\t"
				. __CLASS__. "::" . __FUNCTION__ . "(): " . $e->getMessage();
			$this->recordError($systemMessage);
			$result = $this->getJsonError("Internal server error experienced while rolling back database transaction.");
		}

		self::logExit(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	//************************************************************************************	
	protected function query($mysqli, $query)
	{
		self::logEntry(__CLASS__, __FUNCTION__);

		$result = null;
		$sqlResult = null;
		$array = null;

		try
		{
			if(!$mysqli)
			{
				throw new exception("sql database object undefined!");
			}

			if(!$query)
			{
				throw new exception("query undefined!");
			}
			
			if($this->trace == 1) {
				self::writeln("query=$query");
			}
			
			$mysqli->query("SET SQL_BIG_SELECTS=1");
			$sqlResult = $mysqli->query($query);

			if (!$sqlResult) 
			{
				throw new exception("Database error [" . $mysqli->errno . "]: "
					. $mysqli->error . " at line " . __LINE__ . "\n" . "Query: " . $query);
			}
					
			$result = self::loadResults($sqlResult, "");
		}
		catch(exception $e)
		{
			$systemMessage = "ERROR! " . __CLASS__. "::" . __FUNCTION__ . "(): " . $e->getMessage();
			self::recordError($systemMessage);
			$result = self::getJsonError("Encountered error while receiving results.");
		}

		self::logExit(__CLASS__, __FUNCTION__, $result);
		
		return $result;	
	}

	//************************************************************************************	
	protected function execute($mysqli, $query)
	{
		self::logEntry(__CLASS__, __FUNCTION__);

		$result = null;
		$sqlResult = null;
		$array = null;
		
		try
		{
			if(!$mysqli)
			{
				throw new exception("sql database object undefined!");
			}

			if(!$query)
			{
				throw new exception("query undefined!");
			}

			if($this->trace == 1) {
				self::writeln("statement=$query");
			}
				
			$sqlResult = $mysqli->query($query);

			if (!$sqlResult) 
			{
				throw new exception("Database error [" . $mysqli->errno . "]: "
					. $mysqli->error . " at line " . __LINE__ . "\n" . "Query: " . $query);
			}

			if($this->trace == 1)
			{
				self::writeln("total rows affected=" . $mysqli->affected_rows);
			}
		}
		catch(exception $e)
		{
			$systemMessage = "ERROR! " . __CLASS__. "::" . __FUNCTION__ . "(): " . $e->getMessage();
			self::recordError($systemMessage);
			$result = self::getJsonError("Encountered error while updating information.");
		}

		self::logExit(__CLASS__, __FUNCTION__, $result);
		
		return $result;	
	}
	
	//************************************************************************************
	protected function getNextAutoIncrementNumber($table)
	{
		self::logEntry(__CLASS__, __FUNCTION__);

		$result = null;
		$query = "";

		try
		{
			$mysqli = $this->dblink;
			$query = "SHOW TABLE STATUS LIKE '$table'";
						
			$sqlResult = $mysqli->query($query);
			if (!$sqlResult) {
				throw new exception("Database error [" . $mysqli->errno . "]: "
					. $mysqli->error . " at line " . __LINE__ . "\n" . "Query: " . $query);
			}

			$row = $sqlResult->fetch_assoc();
			$result = $row['Auto_increment'];
		}
		catch(exception $e)
		{
			$systemMessage = "ERROR! " . __FILE__ . "\n\t"
				. __CLASS__. "::" . __FUNCTION__ . "(): " . $e->getMessage();
			$this->recordError($systemMessage);
			$result = $this->getJsonError("Internal server error experienced while retrieving auto increment number.");
		}

		$sqlResult->free();

		self::logExit(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	//************************************************************************************
	protected function getDatabaseLink() {
		return $this->dblink;
	}

	//************************************************************************************
	protected function closeDbConnection()
	{
		self::logEntry(__CLASS__, __FUNCTION__);

		if(isset($this->dblink))
		{
			$mysqli = $this->dblink;
			$mysqli->close();
			unset($this->dblink);
		}

		self::logExit(__CLASS__, __FUNCTION__);
	}	
		
	//************************************************************************************
	protected function findMissingFields($tableName, $mysqli, $preferredColumns, $includePrimaryKey, $includeUniqueKeys)
	{
		self::logEntry(__CLASS__, __FUNCTION__);

		$missingFields = "";
		$result = null;

		try
		{
			if (!isset($mysqli)) {
				throw new exception("Database Link Object Undefined!");
			}

			$query = "SHOW COLUMNS FROM " . $tableName . " ";

			$sqlResult = $mysqli->query($query);
			if (!isset($sqlResult)) {
				throw new exception("Database error [" . $mysqli->errno . "]: "
					. $mysqli->error . " at line " . __LINE__ . "\n" . "Query: " . $query);
			}
			
			$checkFromList = (sizeof($preferredColumns) > 0);
			
			while ($row = $sqlResult->fetch_assoc()) {
				$columnName = $row["Field"];
				$primaryKey = $row["Key"];
				$defaultValue = $row["Default"];
				$requiredField = false;

				if($checkFromList)
				{
					$requiredField = in_array($columnName, $preferredColumns);
				}
				else
				{
					//If the next field is NOT nullable and does NOT have a default value,
					//	it must be user input
					$requiredField = (strtolower($row["Null"]) == "no");					
					if($requiredField)
					{
						$requiredField = strlen($defaultValue) == 0;
						if($requiredField)
						{
							//Are we to include primary keys?
							if(!$includePrimaryKey)
							{
								$requiredField = (strcmp(strtolower($primaryKey), "pri") != 0);
							}
							if($requiredField && !$includeUniqueKeys)
							{
								$requiredField = (strcmp(strtolower($primaryKey), "uni") != 0);
							}
						}
					}
				}
						
				if($requiredField)
				{
					$fieldvalue = $this->getVariable($columnName, null);
					if(strlen($fieldvalue) == 0)
					{
						$missingFields .= $columnName . " is missing.<br />";
					}
					else
					{
						self::setDataItem($columnName, $fieldvalue);
					}
				}
			}			
			
			if(strlen($missingFields) > 0)
			{
				$result = $this->getJsonError($missingFields);
			}
				
			$sqlResult->free();
		}
		catch(exception $e)
		{
			$systemMessage = "ERROR! " . __FILE__ . "\n\t"
				. __CLASS__. "::" . __FUNCTION__ . "(): " . $e->getMessage();
			$this->recordError($systemMessage);
				$result = $this->getJsonError("Error occurred during validation.");
		}

		self::logExit(__CLASS__, __FUNCTION__, $result);
		
		return $result;
	}

	//************************************************************************************
	protected function buildSqlCondition($fields)
	{
		self::logEntry(__CLASS__, __FUNCTION__);

		$result = "";
		
		try
		{
			foreach($fields as $key => $attributes)
			{
				$nextValue = null;
				$tablePrefix = null;
				$minimum = null;
				$maximum = null;
				$addSlashes = "1";
				$comparison = whereOperator::Like;
					
				if(is_array($attributes))
				{
					$queryStringValue = $this->getVariable($key, null);
					$defaultValue = (array_key_exists("defaultValue", $attributes) 
						? $attributes["defaultValue"] 
						: null);
					$operand = (array_key_exists("operand", $attributes) 
						? $attributes["operand"] 
						: null);
					$nextValue = ($operand != null ? $operand : ($queryStringValue != null ? $queryStringValue : $defaultValue));
					$tablePrefix = (array_key_exists("tablePrefix", $attributes) 
						? $attributes["tablePrefix"] . "."
						: null);
					$defaultValue = (array_key_exists("defaultValue", $attributes) 
						? $attributes["defaultValue"] 
						: null);
					$minimum = (array_key_exists("minimum", $attributes) 
						? $attributes["minimum"] 
						: null);
					$maximum = (array_key_exists("maximum", $attributes) 
						? $attributes["maximum"] 
						: null);
					$addSlashes = (array_key_exists("isString", $attributes) 
						? $attributes["isString"] 
						: null);
					$comparison = (array_key_exists("comparison", $attributes) 
						? $attributes["comparison"] 
						: null);
				}
				else
				{
					$nextValue = $this->getVariable($key, null);
				}

				if(strlen($nextValue) > 0)
				{
					$array = explode(",", $nextValue);
					
					if(strcmp($addSlashes, "1") == 0)
					{
						for($i = 0; $i < sizeof($array); $i++)
						{
							$param = addslashes($array[$i]);
							
							if($comparison == whereOperator::Like)
							{
								$param = "%$param%";
							}
							$array[$i] = "'" . $param . "'";
						}
					}
					
					$compareKeyword = (strlen($result) == 0 ? "WHERE" : "AND");
					$values = (strlen($operand) > 0 ? $operand : implode(",", $array));

					switch($comparison)
					{
						case whereOperator::Equal:
						$result .= " $compareKeyword $tablePrefix$key = $values ";
						break;
						case whereOperator::NotEqual:
						$result .= " $compareKeyword $tablePrefix$key <> $values ";
						break;
						case whereOperator::GreaterThan:
						$result .= " $compareKeyword $tablePrefix$key > $values ";
						break;
						case whereOperator::LessThan:
						$result .= " $compareKeyword $tablePrefix$key < $values ";
						break;
						case whereOperator::GreaterThanOrEqual:
						$result .= " $compareKeyword $tablePrefix$key >= $values ";
						break;
						case whereOperator::LessThanOrEqual:
						$result .= " $compareKeyword $tablePrefix$key <= $values ";
						break;
						case whereOperator::Between:
						$result .= " $compareKeyword $tablePrefix$key BETWEEN $minimum AND $maximum ";
						break;
						case whereOperator::Like:
						$result .= " $compareKeyword $tablePrefix$key LIKE $values ";
						break;
						case whereOperator::In:
						$result .= " $compareKeyword $tablePrefix$key IN ($values) ";
						break;
						case whereOperator::Is:
						$result .= " $compareKeyword $tablePrefix$key IS $values ";
						break;
						case whereOperator::IsNot:
						$result .= " $compareKeyword $tablePrefix$key IS NOT $values ";
						break;
						default:
						break;
					}
				}
			}
		}
		catch(exception $e)
		{
			$this->closeDbConnection();	//close the database connection
			$systemMessage = "ERROR! " . __FILE__ . "\n\t"
				. __CLASS__. "::" . __FUNCTION__ . "(): " . $e->getMessage();
			$this->recordError($systemMessage);
			$result = $this->getJsonError("Internal server error experienced while searching for profiles.");
		}

		self::logExit(__CLASS__, __FUNCTION__, $result);
		
		return $result;	
	}
	
	//************************************************************************************
	protected function buildSqlTransactions($fields, $defaultToNull = null)
	{
		self::logEntry(__CLASS__, __FUNCTION__);

		$result = null;
		
		try
		{
			foreach($fields as $key => $list)
			{
				$addSlashes = false;
				$nextValue = null;

				if(is_array($list))
				{
					$addSlashes = $list["isString"];
					$nextValue = (empty($list["operand"]) ? $this->getVariable($key, null) : $list["operand"]);
				}
				else
				{
					$addSlashes = $list;
					$nextValue = $this->getVariable($key, null);
				}

				if(strlen($nextValue) > 0)
				{
					if(strcmp($addSlashes, "1") == 0)
					{
						if(strcmp($key, "password") == 0)
						{
							$nextValue = "'" . addslashes(sha1($nextValue)) . "'";
						}
						else
						{
							$nextValue = "'" . addslashes($nextValue) . "'";
						}
					}
					
					if(strlen($result) == 0)
					{
						$result = " SET $key = $nextValue ";
					}
					else
					{
						$result .= " , $key = $nextValue ";
					}
				}
				else if($defaultToNull) {
					if(strlen($result) == 0)
					{
						$result = " SET $key = NULL ";
					}
					else
					{
						$result .= " , $key = NULL ";
					}
				}
			}
		}
		catch(exception $e)
		{
			$this->closeDbConnection();	//close the database connection
			$systemMessage = "ERROR! " . __FILE__ . "\n\t"
				. __CLASS__. "::" . __FUNCTION__ . "(): " . $e->getMessage();
			$this->recordError($systemMessage);
			$result = $this->getJsonError("Internal server error experienced while searching for profiles.");
		}

		self::logExit(__CLASS__, __FUNCTION__, $result);
		
		return $result;	
	}

	//************************************************************************************
	protected function buildSqlInsertTransactions($fields)
	{
		self::logEntry(__CLASS__, __FUNCTION__);

		$result = "";
		
		try
		{
			foreach($fields as $key => $values)
			{
				$addSlashes = $values;
				$nextValue = $this->getVariable($key, null);
				
				if(is_array($values)) {
					$nextValue = empty($values["operand"]) ? null : $values["operand"];
					$addSlashes = $values["isString"];
				}
				
				if(strcmp(strtolower($addSlashes), "1") == 0 && strlen($nextValue) > 0)
				{
					if(strcmp($key, "password") == 0)
					{
						$nextValue = "'" . addslashes(sha1($nextValue)) . "'";
					}
					else
					{
						$nextValue = "'" . addslashes($nextValue) . "'";
					}
				}
				else if(strlen(trim($nextValue)) == 0)
				{
					$nextValue = "NULL";
				}
				
				if(strlen($result) == 0)
				{
					$result = $nextValue;
				}
				else
				{
					$result .= " , $nextValue";
				}
			}
		}
		catch(exception $e)
		{
			$this->closeDbConnection();	//close the database connection
			$systemMessage = "ERROR! " . __FILE__ . "\n\t"
				. __CLASS__. "::" . __FUNCTION__ . "(): " . $e->getMessage();
			$this->recordError($systemMessage);
			$result = $this->getJsonError("Internal server error experienced while searching for profiles.");
		}

		self::logExit(__CLASS__, __FUNCTION__, $result);

		return $result;	
	}

	//************************************************************************************
	protected function buildInsertSelections($fields)
	{
		self::logEntry(__CLASS__, __FUNCTION__);

		$result = "";
		
		try
		{
			foreach($fields as $key => $values)
			{
				$addSlashes = false;
				$tablePrefix = null;
				$questionPrefix = null;
				$questionPostfix = null;
				$nextValue = null;
				$columnName = $key;
				$columnValue = $this->getVariable($key, null);

				//determine the next value
				if(!is_array($values)) {
					$nextValue = $key;
				}
				else
				{
					if(array_key_exists("isString", $values)) {
						$addSlashes = $values["isString"];
					}
					if(array_key_exists("tablePrefix", $values)) {
						$tablePrefix = $values["tablePrefix"];
					}
					if(array_key_exists("questionPrefix", $values)) {
						$questionPrefix = $values["questionPrefix"];
					}
					if(array_key_exists("questionPostfix", $values)) {
						$questionPostfix = $values["questionPostfix"];
					}

					if(isset($tablePrefix)) {
						$columnName = $tablePrefix . "." . $key;
					}
					
					if(isset($questionPrefix) && isset($questionPostfix)) {
						$nextValue = "  CONCAT('$questionPrefix ', $columnName, ' $questionPostfix') ";
						$addSlashes = null;
					}
					else if(isset($questionPrefix)) {
						$nextValue = "  CONCAT('$questionPrefix ', $columnName) ";
						$addSlashes = null;
					}
					else if(isset($questionPostfix)) {
						$nextValue = "  CONCAT($columnName, ' $questionPostfix') ";
						$addSlashes = null;
					}
					else if(array_key_exists("operand", $values)) {
						$nextValue = $values["operand"];
					}
					else if(isset($columnValue)){
						$nextValue = $columnValue;
					}
					else {
						$addSlashes = null;
					}
					
					if(strcmp(strtolower($addSlashes), "1") == 0 
						&& strlen($nextValue) > 0)
					{
						if(strcmp($key, "password") == 0)
						{
							$nextValue = "'" . addslashes(sha1($nextValue)) . "'";
						}
						else
						{
							$nextValue = "'" . addslashes($nextValue) . "'";
						}
					}
					else if(strlen(trim($nextValue)) == 0)
					{
						$nextValue = $columnName;
					}
				}
				
				if(strlen($result) == 0)
				{
					$result = $nextValue;
				}
				else
				{
					$result .= " , $nextValue";
				}
			}
		}
		catch(exception $e)
		{
			$this->closeDbConnection();	//close the database connection
			$systemMessage = "ERROR! " . __FILE__ . "\n\t"
				. __CLASS__. "::" . __FUNCTION__ . "(): " . $e->getMessage();
			$this->recordError($systemMessage);
			$result = $this->getJsonError("Internal server error experienced while searching for profiles.");
		}

		self::logExit(__CLASS__, __FUNCTION__, $result);

		return $result;	
	}

	//************************************************************************************
	protected function getSqlLimit() 
	{
		$result = null;

		$first = self::getVariable("first", DEFAULT_FIRST_RECORD);
		$length = self::getVariable("length", DEFAULT_TOTAL_RECORDS);
		$result = " LIMIT " . $first . ", " . $length;
		
		self::logExit(__CLASS__, __FUNCTION__, $result);

		return $result;
	}
}

?>