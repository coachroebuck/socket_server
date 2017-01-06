<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

require_once(RMPATH_BASE . DS . API_DIRECTORY . DS . "abstract_model.php");

class abstract_db_table_model extends abstract_model {

	protected $table_name;
	protected $primary_key;
	protected $alias;
	protected $distinct_retrieval_key;
	
	protected function init($object) {
		parent::init($object);
	}

	protected function deinit($object) {
		parent::deinit($object);
		parent::deinit($this);
	}

	public function databaseTableAlias() {
		return empty($this->alias) ? "" : $this->alias;
	}

	public function databaseTableAliasForColumn() {
		return empty($this->alias) ? "" : $this->alias . ".";
	}

	public function databaseTableName() {
		return empty($this->table_name) ? "" : $this->table_name;
	}

	public function primaryKey() {
		return $this->primary_key;
	}

	protected function buildQueryComponents($object, &$fields, &$conditions) {

		log_service::enter_method(__CLASS__, __FUNCTION__);
		
		if(property_exists($object, "table_columns")) {
			$table_columns = $object->table_columns;
			$distinct_retrieval_key = $object->distinct_retrieval_key;
			$alias = $this->databaseTableAliasForColumn();
				
			foreach($table_columns as $key => $table_column_model) {
				$name = $table_column_model->name;
				$data_type = $table_column_model->data_type;
				$is_retrievable = $table_column_model->is_retrievable;
				$condition_info = $table_column_model->condition_info;
				
				if(isset($is_retrievable)) {
					if(!isset($fields) || strlen($fields) == 0) {
						$fields = "SELECT $distinct_retrieval_key $alias$name";
					}
					else {
						$fields .= ",$alias$name";
					}
				}
				
				$value = isset($_GET[$name]) ? $_GET[$name] : null;

				$this->appendConditionsText($conditions, $condition_info, $name, $data_type, $value, $this->alias);
			}
		}
		
		log_service::exit_method(__CLASS__, __FUNCTION__);
	}

	private function appendConditionsText(&$conditions, $condition_info, $name, $data_type, $value, $alias = null) {

		log_service::enter_method(__CLASS__, __FUNCTION__);

		if(isset($condition_info)) {

			$nextCondition = $condition_info->createConditionText($alias, $name, $data_type, $value, null, null);

			if(empty($conditions) && !empty($nextCondition)) {
				$conditions = " WHERE $nextCondition ";
			}
			else if(!empty($nextCondition)) {
				$conditions .= " AND $nextCondition ";
			}
		}
		
		log_service::exit_method(__CLASS__, __FUNCTION__);
	}

	protected function buildInsertComponents($object, &$fields, &$values) {

		log_service::enter_method(__CLASS__, __FUNCTION__);
		
		$array = get_object_vars($object);

		if(property_exists($object, "table_columns")) {
			$table_columns = $object->table_columns;
			$distinct_retrieval_key = $object->distinct_retrieval_key;
				
			foreach($table_columns as $key => $table_column_model) {
				$name = $table_column_model->name;
				$data_type = $table_column_model->data_type;
				$is_insertable = $table_column_model->is_insertable;
				
				if(!empty($is_insertable) && !empty($_POST[$name])) {
					$value = $_POST[$name];
					$fields = empty($fields) ? $name : $fields . "," . $name;

					if($data_type == table_column_data_type::Text) {
						$value = "'" . addslashes($value) . "'";
					}
					$values = empty($values) ? $value : $values . "," . $value;
				}
			}
		}

		log_service::exit_method(__CLASS__, __FUNCTION__, "fields=$fields values=$values");
	}

	private function appendInsertText(&$input, $key, $value) {

		log_service::enter_method(__CLASS__, __FUNCTION__);
		
		if(!isset($input) || strlen($input) == 0) {

			if(is_string($value)) {
				$input = " '" . addslashes($value) . "', ";
			}
			else {
				$input = " $value, ";
			}
		}
		else {
			if(is_string($value)) {
				$input .= " '" . addslashes($value) . "', ";
			}
			else {
				$input .= " $value, ";
			}
		}

		log_service::exit_method(__CLASS__, __FUNCTION__);
	}

	protected function buildUpdateComponents($object, &$fields, &$conditions) {

		log_service::enter_method(__CLASS__, __FUNCTION__);
		
		$array = get_object_vars($object);

		$fields = " SELECT ";
		foreach($array as $key => $value) {
			//TODO: Read from an object
			if(isset($value)) {
				self::appendUpdateStatementText($conditions, $key, $value, $alias);
			}
			else if(isset($_GET[$key])) {
				self::appendUpdateStatementText($conditions, $key, $_GET[$key], $alias);
			}

			if(isset($value)) {
				self::appendConditionsText($conditions, $key, $value, null);
			}
			else if(isset($_GET[$key])) {
				self::appendConditionsText($conditions, $key, $_GET[$key], null);
			}
		}

		$fields = rtrim($fields, ", ");
		
		log_service::exit_method(__CLASS__, __FUNCTION__);
	}

	private function appendUpdateStatementText(&$input, $key, $value) {

		log_service::enter_method(__CLASS__, __FUNCTION__);
		
		if(!isset($input) || strlen($input) == 0) {

			if(is_string($value)) {
				$input = " SET $key = '" . addslashes($value) . "' ";
			}
			else {
				$input = " SET $key = $value ";
			}
		}
		else {
			if(is_string($value)) {
				$input .= " , $key = '" . addslashes($value) . "' ";
			}
			else {
				$input .= " , $key = $value ";
			}
		}

		log_service::exit_method(__CLASS__, __FUNCTION__);
	}

	protected function buildDeleteComponents($object, &$conditions) {

		log_service::enter_method(__CLASS__, __FUNCTION__);
		
		if(property_exists($object, "table_columns")) {
			$table_columns = $object->table_columns;
				
			foreach($table_columns as $key => $table_column_model) {
				$name = $table_column_model->name;
				$data_type = $table_column_model->data_type;
				$condition_info = $table_column_model->condition_info;
				$is_delete_identifier = $table_column_model->is_delete_identifier;
				
				if(!empty($is_delete_identifier) && !empty($_POST[$name])) {
					$value = $_POST[$name];
					$this->appendConditionsText($conditions, $condition_info, $name, $data_type, $value, null);
				}
			}
		}

		log_service::exit_method(__CLASS__, __FUNCTION__, "conditions=$conditions");
	}
}
?>