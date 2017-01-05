<?

// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class validatable_model
{				
	public $enabled;
	public $comparison;
	public $maximumLength;
	public $required;
	public $preAction;
	public $postAction;
	
	//************************************************************************************	
	function __construct($enabled, $required, $comparison, $maximumLength, $preAction, $postAction)
	{
		parent::logEntry(__CLASS__, __FUNCTION__);

		parent::__construct();
		
		$this->enabled = $enabled;
		$this->required = $required;
		$this->comparison = $comparison;
		$this->maximumLength = $maximumLength;
		$this->preAction = $preAction;
		$this->postAction = $postAction;
		
		parent::logExit(__CLASS__, __FUNCTION__);
	}
	
	//************************************************************************************	
	function __construct()
	{
		parent::logEntry(__CLASS__, __FUNCTION__);

		parent::__construct();
		
		$this->enabled = false;
		$this->required = null;
		$this->comparison = null;
		$this->maximumLength = null
		$this->preAction = null;
		$this->postAction = null;
		
		parent::logExit(__CLASS__, __FUNCTION__);
	}
	
	//************************************************************************************	
	function __destruct()
	{
		parent::logEntry(__CLASS__, __FUNCTION__);

		parent::__destruct();

		if(isset($this->enabled)) unset($this->enabled);
		if(isset($this->required)) unset($this->required);
		if(isset($this->comparison)) unset($this->comparison);
		if(isset($this->maximumLength)) unset($this->maximumLength);
		if(isset($this->preAction)) unset($this->preAction);
		if(isset($this->postAction)) unset($this->postAction);
		
		parent::logExit(__CLASS__, __FUNCTION__);
	}
}
?>