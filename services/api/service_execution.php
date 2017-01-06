<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

require_once(RMPATH_BASE . DS . API_DIRECTORY . DS . "abstract_model.php");

class service_execution extends abstract_model {

	private $database_table_object;
	
	function __construct($database_table_object)
	{
		$this->database_table_object = $database_table_object;
	}
	
	public function execute() {

		log_service::enter_method(__CLASS__, __FUNCTION__);

		$method = $_SERVER['REQUEST_METHOD'];
		$result = null;

		switch ($method) {
			case 'GET':
				$result = $this->pre_get();
				if(!isset($result)) $result = $this->get();
				$post_result = $this->post_get();
				if(isset($post_result)) $result = $post_result;
				break;
			
			case 'POST':
				$result = $this->pre_add();
				if(!isset($result)) $result = $this->add();
				if(!isset($result)) {
					$post_result = $this->post_add();
					if(isset($post_result)) $result = $post_result;
				}
				else {
					 $this->rollback();
				}
				
				break;
			
			case 'PUT':
				$result = $this->pre_update();
				if(!isset($result)) $result = $this->update();
				if(!isset($result)) {
					$post_result = $this->post_update();
					if(isset($post_result)) $result = $post_result;
				}
				else {
					 $this->rollback();
				}
				break;
			
			case 'DELETE':
				$result = $this->pre_delete();
				if(!isset($result)) $result = $this->delete();
				if(!isset($result)) {
					$post_result = $this->post_delete();
					if(isset($post_result)) $result = $post_result;
				}
				else {
					 $this->rollback();
				}
				break;
			
			default:
				$result = "request method [$method] has not been implemented.";
				break;
		}

		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}
	
	private function total() {
		
	}

	private function random() {
		
	}

	private function get() {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		$result = $this->database_table_object->get();
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
		return $result;
	}

	private function pre_get() {
		
	}

	private function post_get() {
		
	}

	private function add() {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		$result =  $this->database_table_object->add();
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
		return $result;
	}

	private function pre_add() {
		$this->start();
	}

	private function post_add() {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		$result = $this->database_table_object->post_add();
		$this->commit();
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
		return $result;
	}

	private function update() {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		$result =  $this->database_table_object->update();
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
		return $result;
	}

	private function pre_update() {
		$this->start();
	}

	private function post_update() {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		$result = $this->database_table_object->get();
		$this->commit();
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
	}

	private function delete() {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		$result =  $this->database_table_object->delete();
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
		return $result;
	}

	private function pre_delete() {
		$this->start();
	}

	private function post_delete() {
		$this->commit();		
	}

	private function start() {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		$result =  $this->database_table_object->start();
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
		return $result;
	}

	private function commit() {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		$result =  $this->database_table_object->commit();
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
		return $result;
	}

	private function rollback() {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		$result =  $this->database_table_object->rollback();
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
		return $result;
	}

	function __destruct()
	{
		parent::deinit($this);
	}
}
?>