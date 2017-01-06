<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

require_once(RMPATH_BASE . DS . API_DIRECTORY . DS . "abstract_object.php");

class service_execution extends abstract_object {

	private $database_object;
	
	function __construct($database_object)
	{
		$this->database_object = $database_object;
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
				$post_result = $this->post_add();
				if(isset($post_result)) $result = $post_result;
				break;
			
			case 'PUT':
				$result = $this->pre_update();
				if(!isset($result)) $result = $this->update();
				$post_result = $this->post_update();
				if(isset($post_result)) $result = $post_result;
				break;
			
			case 'DELETE':
				$result = $this->pre_delete();
				if(!isset($result)) $result = $this->delete();
				$post_result = $this->post_delete();
				if(isset($post_result)) $result = $post_result;
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
		$result = $this->database_object->get();
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
		return $result;
	}

	private function pre_get() {
		
	}

	private function post_get() {
		
	}

	private function add() {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		$result =  $this->database_object->add();
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
		return $result;
	}

	private function pre_add() {
		
	}

	private function post_add() {
		
	}

	private function update() {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		$result =  $this->database_object->update();
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
		return $result;
	}

	private function pre_update() {
		
	}

	private function post_update() {
		
	}

	private function delete() {
		log_service::enter_method(__CLASS__, __FUNCTION__);
		$result =  $this->database_object->delete();
		log_service::exit_method(__CLASS__, __FUNCTION__, $result);
		return $result;
	}

	private function pre_delete() {
		
	}

	private function post_delete() {
		
	}

	function __destruct()
	{
		parent::deinit($this);
	}
}
?>