<?

// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class editable_model
{				
	public $enabled;
	public $operand;
	public $isOperandStashed;
	
	//************************************************************************************	
	function __construct($enabled, $operand, $isOperandStashed)
	{
		parent::logEntry(__CLASS__, __FUNCTION__);

		parent::__construct();
		
		$this->enabled = $enabled;
		$this->operand = $operand;
		$this->isOperandStashed = $isOperandStashed;
		
		parent::logExit(__CLASS__, __FUNCTION__);
	}
	
	//************************************************************************************	
	function __construct()
	{
		parent::logEntry(__CLASS__, __FUNCTION__);

		parent::__construct();
		
		$this->enabled = false;
		$this->operand = null;
		$this->isOperandStashed = false;
		
		parent::logExit(__CLASS__, __FUNCTION__);
	}
	
	//************************************************************************************	
	function __destruct()
	{
		parent::logEntry(__CLASS__, __FUNCTION__);

		parent::__destruct();

		if(isset($this->enabled)) unset($this->enabled);
		if(isset($this->operand)) unset($this->operand);
		if(isset($this->isOperandStashed)) unset($this->isOperandStashed);
		
		parent::logExit(__CLASS__, __FUNCTION__);
	}
}
?>