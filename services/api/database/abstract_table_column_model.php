<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class abstract_table_column_model extends abstract_model {
	public $name;
	public $data_type;
	public $validation_info;
	public $condition_info;
	public $is_insertable;
	public $is_retrievable;
	public $update_info;
	public $is_delete_identifier;
	public $column_alias;
	public $encrpytion_required;
	public $default_value;
	

	function __construct(
		$name = null,
		$data_type = null,
		$validation_info = null,
		$condition_info = null,
		$is_retrievable = null,
		$is_insertable = null,
		$update_info = null,
		$is_delete_identifier = null,
		$column_alias = null,
		$encrpytion_required = null,
		$default_value = null)
	{
		$this->name = $name;
		$this->data_type = $data_type;
		$this->validation_info = $validation_info;
		$this->condition_info = $condition_info;
		$this->is_retrievable = $is_retrievable;
		$this->is_insertable = $is_insertable;
		$this->update_info = $update_info;
		$this->is_delete_identifier = $is_delete_identifier;
		$this->column_alias = $column_alias;
		$this->encrpytion_required = $encrpytion_required;
		$this->default_value = $default_value;
	}
	
	function __destruct()
	{
		parent::deinit($this);
	}
}

?>