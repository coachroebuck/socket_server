<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class abstract_model {

	function __construct() {

	}
	
	function __destruct() {
	}

	protected function init($object) {

	}

	protected function deinit($object) {
		$properties = get_object_vars($object);
		foreach($properties as $nextProperty) {
			if(isset($nextProperty)) {
				unset($nextProperty);
			}
		}
	}
}
?>