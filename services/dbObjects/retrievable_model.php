<?

// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class retrievable_model
{				
	public $enabled;
	public $aliasKey;
	public $aliasValue;
	
	//************************************************************************************	
	function __construct($enabled, $aliasKey, $aliasValue)
	{
		parent::logEntry(__CLASS__, __FUNCTION__);

		parent::__construct();
		
		$this->enabled = $enabled;
		$this->aliasKey = $aliasKey;
		$this->aliasValue = $aliasValue;
		
		parent::logExit(__CLASS__, __FUNCTION__);
	}
	
	//************************************************************************************	
	function __construct()
	{
		parent::logEntry(__CLASS__, __FUNCTION__);

		parent::__construct();
		
		$this->enabled = false;
		$this->aliasKey = null;
		$this->aliasValue = null;
		
		parent::logExit(__CLASS__, __FUNCTION__);
	}
	
	//************************************************************************************	
	function __destruct()
	{
		parent::logEntry(__CLASS__, __FUNCTION__);

		parent::__destruct();

		if(isset($this->enabled)) unset($this->enabled);
		if(isset($this->aliasKey)) unset($this->aliasKey);
		if(isset($this->aliasValue)) unset($this->aliasValue);
		
		parent::logExit(__CLASS__, __FUNCTION__);
	}
}
?>