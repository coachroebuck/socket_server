<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

require_once ( RMPATH_BASE.DS.'baseObject.php');
require_once ( BIN_DIRECTORY.DS.'ucUtility.php');

class database_base_model
{				
	public $tableName = null;
	public $tableInitials = null;
	public $joinTablesDuringGet = null;
	public $joinTablesDuringInsertSelect = null;
	public $primaryKey = null;
	public $tableColumns = null;
	public $distinctKeyword = null;
	
	//************************************************************************************	
	function __construct()
	{
		parent::__construct();
		if(empty($this->tableName)) $this->tableName = get_class($this);
	}
	
	//************************************************************************************	
	function __destruct()
	{
		unset($this->tableName);
		unset($this->tableInitials);
		unset($this->joinTablesDuringGet);
		unset($this->joinTablesDuringInsertSelect);
		unset($this->primaryKey);
		unset($this->tableColumns);
		unset($this->distinctKeyword);
	}
}
?>