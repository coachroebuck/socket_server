<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

require_once(RMPATH_BASE . DS . API_DIRECTORY . DS . "abstract_model.php");

class abstract_db_table_object extends abstract_model {
	
	private $db;
	private $database_table_model;
	
	public function initialize(
		$db = null, 
		$database_table_model = null)
	{
		log_service::enter_method(__CLASS__, __FUNCTION__);

		$this->db = $db;
		$this->database_table_model = $database_table_model;
		
		$str = "db=[" . isset($db) . "] database_table_model=[" . isset($database_table_model) . "]";
		log_service::exit_method(__CLASS__, __FUNCTION__, $str);
	}

	public function total() {
		
		log_service::enter_method(__CLASS__, __FUNCTION__);
		
	}

	public function random() {
		
		log_service::enter_method(__CLASS__, __FUNCTION__);
		
	}

	public function get() {

		log_service::enter_method(__CLASS__, __FUNCTION__);

		$fields = null;
		$conditions = null;
		$alias = $this->database_table_model->databaseTableAlias();
		$table_name = $this->database_table_model->databaseTableName();
		
		$this->database_table_model->queryComponents($fields, $conditions);
		$query = "$fields FROM " 
			. DATABASE_NAME . ".$table_name $alias $conditions";
		$result = $this->db->query($query);
		
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	public function add() {

		log_service::enter_method(__CLASS__, __FUNCTION__);

		$fields = null;
		$values = null;
		$table_name = $this->database_table_model->databaseTableName();
		
		$this->database_table_model->insertComponents($fields, $values);
		$statement = "INSERT INTO " 
			. DATABASE_NAME . "." . $table_name
			. " ($fields) VALUES ($values) ";
		$result = $this->db->execute($statement);
		
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	public function post_add() {

		log_service::enter_method(__CLASS__, __FUNCTION__);

		$fields = null;
		$conditions = null;
		$id = $this->db->getLatestAutoIncrementId();
		$alias = $this->database_table_model->databaseTableAlias();
		$table_name = $this->database_table_model->databaseTableName();
		$primary_key = $this->database_table_model->primaryKey();
		
		$this->database_table_model->queryComponents($fields, $conditions);
		$query = "$fields FROM " 
			. DATABASE_NAME . "." . $table_name
			. " $alias WHERE $primary_key = $id";
		$result = $this->db->query($query);
		
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	public function update() {

		log_service::enter_method(__CLASS__, __FUNCTION__);

		$fields = null;
		$conditions = null;
		$table_name = $this->database_table_model->databaseTableName();
		
		$this->database_table_model->updateComponents($fields, $conditions);
		$statement = "UPDATE " 
			. DATABASE_NAME . "." . $table_name
			. " $fields $conditions ";
		$result = $this->db->execute($statement);
		
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	public function post_update() {

		log_service::enter_method(__CLASS__, __FUNCTION__);

		$fields = null;
		$conditions = null;
		$alias = $this->database_table_model->databaseTableAlias();
		$table_name = $this->database_table_model->databaseTableName();
		$primary_key = $this->database_table_model->primaryKey();
		$value = $this->database_table_model->valueOfPrimaryKey();
		
		$this->database_table_model->queryComponents($fields, $conditions);
		$query = "$fields FROM " 
			. DATABASE_NAME . "." . $table_name
			. " $alias WHERE $primary_key = $value";
		$result = $this->db->query($query);
		
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	public function delete() {

		log_service::enter_method(__CLASS__, __FUNCTION__);

		$conditions = null;
		$table_name = $this->database_table_model->databaseTableName();
		
		$this->database_table_model->deleteComponents($conditions);
		$statement = "DELETE FROM " 
			. DATABASE_NAME . ".$table_name $conditions ";
		$result = $this->db->execute($statement);
		
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	public function start() {

		log_service::enter_method(__CLASS__, __FUNCTION__);

		$result = $this->db->startTransaction();
		
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	public function commit() {

		log_service::enter_method(__CLASS__, __FUNCTION__);

		$result = $this->db->endTransaction();
		
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	public function rollback() {

		log_service::enter_method(__CLASS__, __FUNCTION__);

		$result = $this->db->rollbackTransaction();
		
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	function __destruct()
	{
		parent::deinit($this);
	}
}
?>