<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

require_once(RMPATH_BASE . DS . API_DIRECTORY . DS . "abstract_condition_model.php");
require_once(RMPATH_BASE . DS . API_DIRECTORY . DS . "abstract_db_table_model.php");
require_once(RMPATH_BASE . DS . API_DIRECTORY . DS . "abstract_table_column_model.php");

class oauth_scopes_model extends abstract_db_table_model {
	
	public $table_columns;
	
	function __construct()
	{
		$this->table_name = str_replace("_model", "", get_class($this));
		$this->primary_key = "scope";
		$this->alias = "os";
		$this->distinct_retrieval_key = "";

		$table_columns = array();
		array_push($table_columns, 
			new abstract_table_column_model("scope", table_column_data_type::Text, null, 
				new abstract_condition_model(condition_operator::Equal), 
				true, null, null, 
				true));
		array_push($table_columns, 
			new abstract_table_column_model("is_default", table_column_data_type::Number, null,  
				new abstract_condition_model(condition_operator::Equal), 
				true, true, null, null));
		$this->table_columns = $table_columns;
	}

	public function queryComponents(&$fields, &$conditions) {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		parent::buildQueryComponents($this, $fields, $conditions);
		log_service::exit_method(__CLASS__, __FUNCTION__);
	}

	public function insertComponents(&$fields, &$values) {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		parent::buildInsertComponents($this, $fields, $values);
		log_service::exit_method(__CLASS__, __FUNCTION__);
	}

	public function updateComponents(&$fields, &$conditions) {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		parent::buildUpdateComponents($this, $fields, $conditions);
		log_service::exit_method(__CLASS__, __FUNCTION__);
	}

	public function deleteComponents(&$conditions) {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		parent::buildDeleteComponents($this, $conditions);
		log_service::exit_method(__CLASS__, __FUNCTION__);
	}
	
	function __destruct()
	{
		parent::deinit($this);
	}
}

?>