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

	public function valueOfPrimaryKey() {

		log_service::enter_method(__CLASS__, __FUNCTION__);

		$result = null;
		
		if(property_exists($this, "table_columns")) {
			$table_columns = $this->table_columns;
				
			foreach($table_columns as $key => $table_column_model) {
				$name = $table_column_model->name;
				$data_type = $table_column_model->data_type;
				$condition_info = $table_column_model->condition_info;
				
				if(strcmp($name, $this->primary_key) == 0) {
					$result = $_POST[$name];
					if($data_type == table_column_data_type::Text) {
						$value = "'" . addslashes($value) . "'";
					}
					break;
				}
			}
		}
		
		log_service::exit_method(__CLASS__, __FUNCTION__);

		return $result;
	}

	public function queryComponents(&$fields, &$conditions) {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		self::buildQueryComponents($this, $fields, $conditions);
		log_service::exit_method(__CLASS__, __FUNCTION__);
	}

	public function insertComponents(&$fields, &$values) {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		self::buildInsertComponents($this, $fields, $values);
		log_service::exit_method(__CLASS__, __FUNCTION__);
	}

	public function updateComponents(&$fields, &$conditions) {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		self::buildUpdateComponents($this, $fields, $conditions);
		log_service::exit_method(__CLASS__, __FUNCTION__);
	}

	public function deleteComponents(&$conditions) {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		self::buildDeleteComponents($this, $conditions);
		log_service::exit_method(__CLASS__, __FUNCTION__);
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
				$column_alias = $table_column_model->column_alias;
				
				if(isset($is_retrievable)) {
					if(!isset($fields) || strlen($fields) == 0) {
						$fields = " SELECT $distinct_retrieval_key $alias$name ";
					}
					else {
						$fields .= " ,$alias$name ";
					}

					if(!empty($column_alias)) {
						$fields .= " AS $column_alias ";
					}
				}
				
				$value = isset($_GET[$name]) ? $_GET[$name] : null;

				$this->appendConditionsText($conditions, $condition_info, $name, $data_type, $value, $this->alias);
			}
		}
		
		log_service::exit_method(__CLASS__, __FUNCTION__);
	}

	private function appendConditionsText(&$conditions, $condition_info, $name, $data_type, $value, $alias = null) {

		if(isset($condition_info)) {

			$nextCondition = $condition_info->createConditionText($alias, $name, $data_type, $value, null, null);

			if(empty($conditions) && !empty($nextCondition)) {
				$conditions = " WHERE $nextCondition ";
			}
			else if(!empty($nextCondition)) {
				$conditions .= " AND $nextCondition ";
			}
		}
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
				$encrpytion_required = $table_column_model->encrpytion_required;

				if(!empty($is_insertable) && !empty($_POST[$name])) {
					$value = $_POST[$name];
					$fields = empty($fields) ? $name : $fields . "," . $name;

					if($data_type == table_column_data_type::Text) {
						if(!empty($encrpytion_required)) {
							$value = "'" . addslashes(sha1($value)) . "'";
						}
						else {
							$value = "'" . addslashes($value) . "'";
						}
					}
					$values = empty($values) ? $value : $values . "," . $value;
				}
			}
		}

		log_service::exit_method(__CLASS__, __FUNCTION__, "fields=$fields values=$values");
	}

	protected function buildUpdateComponents($object, &$fields, &$conditions) {

		log_service::enter_method(__CLASS__, __FUNCTION__);
		
		$array = get_object_vars($object);

		if(property_exists($object, "table_columns")) {
			$table_columns = $object->table_columns;
			$distinct_retrieval_key = $object->distinct_retrieval_key;
				
			foreach($table_columns as $key => $table_column_model) {
				$name = $table_column_model->name;
				$data_type = $table_column_model->data_type;
				$is_insertable = $table_column_model->is_insertable;
				$condition_info = $table_column_model->condition_info;
				$is_delete_identifier = $table_column_model->is_delete_identifier;
				
				if(!empty($is_delete_identifier) && !empty($_POST[$name])) {
					$value = $_POST[$name];
					$this->appendConditionsText($conditions, $condition_info, $name, $data_type, $value, null);
				}
				else if(!empty($is_insertable) && !empty($_POST[$name])) {
					$value = $_POST[$name];
					$this->appendUpdateStatementText($fields, $name, $data_type, $value);
				}
			}
		}

		log_service::exit_method(__CLASS__, __FUNCTION__, "conditions=$conditions");
	}

	private function appendUpdateStatementText(&$input, $key, $data_type, $value) {

		if($data_type == table_column_data_type::Text) {
			$value = "'" . addslashes($value) . "'";
		}

		$input = empty($input) ? " SET $key=$value " : $input . ", $key=$value ";
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