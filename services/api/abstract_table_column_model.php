<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

require_once(RMPATH_BASE . DS . API_DIRECTORY . DS . "abstract_model.php");

class abstract_table_column_model extends abstract_model {
	public $name;
	public $data_type;
	public $validation_info;
	public $condition_info;
	public $is_insertable;
	public $is_retrievable;
	public $update_info;
	public $is_delete_identifier;
	

	function __construct(
		$name = null,
		$data_type = null,
		$validation_info = null,
		$condition_info = null,
		$is_retrievable = null,
		$is_insertable = null,
		$update_info = null,
		$is_delete_identifier = null)
	{
		$this->name = $name;
		$this->data_type = $data_type;
		$this->validation_info = $validation_info;
		$this->condition_info = $condition_info;
		$this->is_retrievable = $is_retrievable;
		$this->is_insertable = $is_insertable;
		$this->update_info = $update_info;
		$this->is_delete_identifier = $is_delete_identifier;
	}
	
	function __destruct()
	{
		parent::deinit($this);
	}
}

?>