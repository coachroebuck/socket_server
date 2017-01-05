<?

// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class table_column_model
{				
	public $key;
	public $tablePrefix;
	public $isString;
	public $validatable;
	public $conditionable;
	public $insertable;
	public $updateable;
	public $retrievable;
	public $deletable;
	
	//************************************************************************************	
	function __construct($key, $tablePrefix, $isString, $validatable, $conditionable, $insertable, $updateable, $retrievable, $deletable)
	{
		parent::logEntry(__CLASS__, __FUNCTION__);

		parent::__construct();
		
		$this->key = $key;
		$this->tablePrefix = $tablePrefix;
		$this->isString = $isString;
		$this->validatable = $validatable;
		$this->conditionable = $conditionable;
		$this->insertable = $insertable;
		$this->updateable = $updateable;
		$this->retrievable = $retrievable;
		$this->deletable = $deletable;
		
		parent::logExit(__CLASS__, __FUNCTION__);
	}
	
	//************************************************************************************	
	function __construct()
	{
		parent::logEntry(__CLASS__, __FUNCTION__);

		parent::__construct();
		
		$this->key;
		$this->tablePrefix;
		$this->isString = false;
		$this->validatable;
		$this->conditionable;
		$this->insertable;
		$this->updateable;
		$this->retrievable;
		$this->deletable;
		
		parent::logExit(__CLASS__, __FUNCTION__);
	}
	
	//************************************************************************************	
	function __destruct()
	{
		parent::logEntry(__CLASS__, __FUNCTION__);

		parent::__destruct();

		if(isset($this->key)) unset($this->key);
		if(isset($this->tablePrefix)) unset($this->tablePrefix);
		if(isset($this->isString)) unset($this->isString);
		if(isset($this->validatable)) unset($this->validatable);
		if(isset($this->conditionable)) unset($this->conditionable);
		if(isset($this->insertable)) unset($this->insertable);
		if(isset($this->updateable)) unset($this->updateable);
		if(isset($this->retrievable)) unset($this->retrievable);
		if(isset($this->deletable)) unset($this->deletable);

		parent::logExit(__CLASS__, __FUNCTION__);
	}
}
?>