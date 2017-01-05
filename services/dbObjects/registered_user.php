<?

// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

require_once("../definitions.php");
require_once("table_column_model.php");
require_once("whereOperator.php");
require_once("retrievable_model.php");
require_once("editable_model.php");

class registered_user : database_base_model
{				
	private $primaryKey;
	//************************************************************************************	
	function __construct()
	{
		parent::logEntry(__CLASS__, __FUNCTION__);

		parent::__construct();
		
		$this->primaryKey = "userId";
		$this->tableInitials = "u";
		$this->tableColumns = array();

		$this->tableColumns[$this->primaryKey] = new table_column_model(
			$this->primaryKey, 
			$this->tableInitials, 
			false, 
			null, 
			new editable_model(true, whereOperator::In, null), 
			null, 
			null, 
			new retrievable_model(true, null, null), 
			new editable_model(true, whereOperator::In, null)
		);
		$this->tableColumns["firstName"] = new table_column_model(
			"firstName", 
			$this->tableInitials, 
			false, 
			new validatable_model(true, true, whereOperator::In, MAXIMUM_SHORTFIELD_CHARACTERS, null, null), 
			new condition_model(true, whereOperator::In, null, true), 
			new editable_model(true, null, true), 
			new editable_model(true, null, true), 
			new retrievable_model(true, null, null), 
			new editable_model(true, null, true)
		);
		$this->tableColumns["lastName"] = new table_column_model(
			"lastName", 
			$this->tableInitials, 
			false, 
			new validatable_model(true, true, whereOperator::In, MAXIMUM_SHORTFIELD_CHARACTERS, null, null), 
			new condition_model(true, whereOperator::In, null, true), 
			new editable_model(true, null, true), 
			new editable_model(true, null, true), 
			new retrievable_model(true, null, null), 
			new editable_model(true, null, true)
		);
		$this->tableColumns["nickName"] = new table_column_model(
			"nickName", 
			$this->tableInitials, 
			false, 
			new validatable_model(true, true, whereOperator::In, MAXIMUM_SHORTFIELD_CHARACTERS, null, null), 
			new condition_model(true, whereOperator::In, null, true), 
			new editable_model(true, null, true), 
			new editable_model(true, null, true), 
			new retrievable_model(true, null, null), 
			new editable_model(true, null, true)
		);
		$this->tableColumns["email"] = new table_column_model(
			"email", 
			$this->tableInitials, 
			false, 
			new validatable_model(true, true, whereOperator::In, MAXIMUM_SHORTFIELD_CHARACTERS, null, null), 
			new condition_model(true, whereOperator::In, null, true), 
			new editable_model(true, null, true), 
			new editable_model(true, null, true), 
			new retrievable_model(true, null, null), 
			new editable_model(true, null, true)
		);
		$this->tableColumns["password"] = new table_column_model(
			"password", 
			$this->tableInitials, 
			false, 
			new validatable_model(true, true, whereOperator::In, MAXIMUM_SHORTFIELD_CHARACTERS, null, null), 
			null, 
			new editable_model(true, null, true), 
			new editable_model(true, null, true), 
			null, 
			null
		);
		$this->tableColumns["dateCreated"] = new table_column_model(
			"dateCreated", 
			$this->tableInitials, 
			false, 
			null, 
			null, 
			new editable_model(true, CURRENT_TIMESTAMP_KEYWORD, false), 
			null, 
			new retrievable_model(true, UNIX_TIMESTAMP_KEYWORD 
				. "(" . TABLE_ALIAS_PLACEHOLDER . "." . TABLE_COLUMNNAME_PLACEHOLDER 
				. ")", null),
			null
		);
		$this->tableColumns["lastModifiedDate"] = new table_column_model(
			"lastModifiedDate", 
			$this->tableInitials, 
			false, 
			null, 
			null, 
			null, 
			new editable_model(true, CURRENT_TIMESTAMP_KEYWORD, false), 
			new retrievable_model(true, UNIX_TIMESTAMP_KEYWORD 
				. "(" . TABLE_ALIAS_PLACEHOLDER . "." . TABLE_COLUMNNAME_PLACEHOLDER 
				. ")", null),
			null
		);

		parent::logExit(__CLASS__, __FUNCTION__);
	}
	
	//************************************************************************************	
	function __destruct()
	{
		parent::logEntry(__CLASS__, __FUNCTION__);

		parent::__destruct();

		parent::logExit(__CLASS__, __FUNCTION__);
	}
}
?>