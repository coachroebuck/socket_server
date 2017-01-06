<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class database_reference {

	private $dblink;

	static public function create() {
		return new database_reference();
	}

	function __construct()
	{
	}

	function __destruct()
	{
		self::close();
	}

	//************************************************************************************
	public function open()
	{
		log_service::enter_method(__CLASS__, __FUNCTION__);

		$result = null;

		try
		{
			$this->dbserver = DB_SERVER;
			$this->dbname = DB_NAME;
			$this->dbuser = DB_USER;
			$this->dbpwd = DB_PASSWORD;
			if(isset($this->dblink)) $this->closeDbConnection();
			$this->dblink = mysqli_connect($this->dbserver, $this->dbuser, $this->dbpwd, $this->dbname);
			$this->dblink->query("SET CHARACTER SET utf8");
	
			if(!$this->dblink)
			{
				throw new exception("Database error [" . mysqli_connect_errno() . "]: " . mysqli_connect_error() . "\n");
			}
		}
		catch(exception $e) {
			$result = log_service::error($e, __CLASS__, __FUNCTION__, "Internal server error occurred while opening the database.");
		}

		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	//************************************************************************************
	public function startTransaction()
	{
		log_service::enter_method(__CLASS__, __FUNCTION__);

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
		catch(exception $e) {
			$result = log_service::error($e, __CLASS__, __FUNCTION__, "Internal server error occurred while starting database transaction.");
		}

		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	//************************************************************************************
	public function endTransaction()
	{
		log_service::enter_method(__CLASS__, __FUNCTION__);

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
		catch(exception $e) {
			$result = log_service::error($e, __CLASS__, __FUNCTION__, "Internal server error occurred while ending database transaction.");
		}

		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	//************************************************************************************
	public function rollbackTransaction()
	{
		log_service::enter_method(__CLASS__, __FUNCTION__);

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
		catch(exception $e) {
			$result = log_service::error($e, __CLASS__, __FUNCTION__, "Internal server error occurred while reverting database transaction.");
		}

		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}
	
	//************************************************************************************	
	public function query($query)
	{
		log_service::enter_method(__CLASS__, __FUNCTION__);

		$result = null;
		$sqlResult = null;
		$array = null;
		$mysqli = $this->dblink;

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
			
			if(APPLICATION_TRACE == 1) {
				log_service::writeln("query=$query");
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
		catch(exception $e) {
			$result = log_service::error($e, __CLASS__, __FUNCTION__, "Encountered error while receiving results.");
		}

		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;	
	}

	//************************************************************************************	
	public function execute($query)
	{
		log_service::enter_method(__CLASS__, __FUNCTION__);

		$result = null;
		$sqlResult = null;
		$array = null;
		$mysqli = $this->dblink;

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

			if(APPLICATION_TRACE == 1) {
				log_service::writeln("statement=$query");
			}
				
			$sqlResult = $mysqli->query($query);

			if (!$sqlResult) 
			{
				throw new exception("Database error [" . $mysqli->errno . "]: "
					. $mysqli->error . " at line " . __LINE__ . "\n" . "Query: " . $query);
			}

			if(APPLICATION_TRACE == 1)
			{
				log_service::writeln("total rows affected=" . $mysqli->affected_rows);
			}
		}
		catch(exception $e)
		{
			$result = log_service::error($e, __CLASS__, __FUNCTION__, "Encountered error while updating information.");
		}

		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
		
		return $result;	
	}
	
	//************************************************************************************
	private function loadResults($sqlResult, $noInfoMessage)
	{
		log_service::enter_method(__CLASS__, __FUNCTION__);

		$result = null;
		$query  = null;
		$array = null;
		
		try
		{
			if(!$sqlResult)
			{
				throw new exception("sqlResult object is not initialized!");
			}
			
			if(strlen($noInfoMessage) > 0)
			{
				$result = $this->getJsonNotice(strlen($noInfoMessage) > 0 ? $noInfoMessage : "No results found.");
			}
			else
			{
				$array = array();			
				if(mysqli_num_rows($sqlResult) > 0)
				{
					$sqlResult->data_seek(0);
					while ($row = $sqlResult->fetch_assoc()) {
						$array[sizeof($array)] = $row;
					}
				}
				
				$sqlResult->free();

				if(sizeof($array) > 0) 
				{
					$result = json_encode(sizeof($array) > 1 ? $array : $array[0], 
						JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
				}
			}
		}
		catch(exception $e)
		{
			$result = log_service::error($e, __CLASS__, __FUNCTION__, "Encountered error while loading retrieved information.");
		}

		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
		
		return $result;	
	}
	
	//************************************************************************************
	public function getNextAutoIncrementNumber($table)
	{
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
		catch(exception $e) {
			$result = log_service::error($e, __CLASS__, __FUNCTION__, "Internal server error occurred while retrieving auto increment number.");
		}

		$sqlResult->free();

		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	//************************************************************************************
	public function close()
	{
		if(isset($this->dblink))
		{
			$mysqli = $this->dblink;
			$mysqli->close();
			unset($this->dblink);
		}

		log_service::exit_method(__CLASS__, __FUNCTION__);
	}	
}

?>