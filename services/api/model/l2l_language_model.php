<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

require_once(RMPATH_BASE . DS . API_DIRECTORY . DS . "abstract_object.php");

class l2l_language_model extends abstract_object {
	public $language_id;
	public $language_name;
	public $language_code;
	public $native_language_name;
	public $language_change_user_id;
	public $date_created;
	public $last_modified_date;

	function __construct(
		$language_id = null,
	 	$language_name = null,
	  	$language_code = null, 
	  	$native_language_name = null, 
	  	$language_change_user_id = null,
	  	$date_created = null,
	  	$last_modified_date = null)
	{
		$this->language_id = $language_id;
		$this->language_name = $language_name;
		$this->language_code = $language_code;
		$this->native_language_name = $native_language_name;
		$this->language_change_user_id = $language_change_user_id;
		$this->date_created = $date_created;
		$this->last_modified_date = $last_modified_date;
	}

	public function queryComponents(&$fields, &$conditions, $alias) {
		parent::buildQueryComponents($this, $fields, $conditions, $alias);
	}
	
	function __destruct()
	{
		parent::deinit($this);
	}
}

?>