<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

require_once(RMPATH_BASE . DS . API_DIRECTORY . DS . "abstract_condition_model.php");
require_once(RMPATH_BASE . DS . API_DIRECTORY . DS . "abstract_db_table_model.php");
require_once(RMPATH_BASE . DS . API_DIRECTORY . DS . "abstract_table_column_model.php");

class l2l_language_model extends abstract_db_table_model {
	
	public $table_columns;
	
	function __construct()
	{
		$this->table_name = str_replace("_model", "", get_class($this));
		$this->primary_key = "language_id";
		$this->alias = "l";
		$this->distinct_retrieval_key = "";

		$table_columns = array();
		array_push($table_columns, 
			new abstract_table_column_model("language_id", table_column_data_type::Number, null, 
				new abstract_condition_model(condition_operator::In), 
				true, null, null, 
				true));
		array_push($table_columns, 
			new abstract_table_column_model("language_name", table_column_data_type::Text, null, 
				new abstract_condition_model(condition_operator::In), 
				true, true, null, null));
		array_push($table_columns, 
			new abstract_table_column_model("language_code", table_column_data_type::Text, null,  
				new abstract_condition_model(condition_operator::In), 
				true, true, null, null));
		array_push($table_columns, 
			new abstract_table_column_model("native_language_name", table_column_data_type::Text, null,  
				new abstract_condition_model(condition_operator::In), 
				true, true, null, null));
		array_push($table_columns, 
			new abstract_table_column_model("language_change_user_id", table_column_data_type::Text, null, null, null, null, null, null));
		array_push($table_columns, 
			new abstract_table_column_model("date_created", "number", null, null, true, null, null, null));
		array_push($table_columns, 
			new abstract_table_column_model("last_modified_date", "number", null, null, true, null, null, null));
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