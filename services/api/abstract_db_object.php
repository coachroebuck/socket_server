<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

require_once(RMPATH_BASE . DS . API_DIRECTORY . DS . "abstract_object.php");

class abstract_db_object extends abstract_object {
	
	private $db;
	private $l2l_model;
	private $table_name;
	
	public function initialize(
		$db = null, 
		$table_name = null,
		$l2l_model = null)
	{
		$this->db = $db;
		$this->table_name = $table_name;
		$this->l2l_model = $l2l_model;
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
		$alias = "l";

		$this->l2l_model->queryComponents($fields, $conditions, $alias);
		$query = "$fields FROM " 
			. DATABASE_NAME . "." . $this->table_name 
			. " $alias $conditions";
		$result = $this->db->query($query);
		
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}

	public function add() {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		$result = null;
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
	}

	public function update() {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		$result = null;
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
	}

	public function delete() {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		$result = null;
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
	}

	function __destruct()
	{
		parent::deinit($this);
	}
}
?>