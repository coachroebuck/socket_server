<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class abstract_factory {

	static public function database_object($db, $model) {
		$obj = new abstract_db_table_object();
		$obj->initialize($db, $model);
		return $obj;
	}

	static public function database_model($name) {
		$obj = "l2l_" . $name . "_model";
		if(class_exists($obj)) {
			return new $obj();
		}
		$obj = "oauth_" . $name . "_model";
		if(class_exists($obj)) {
			return new $obj();
		}
		return null;
	}
}
?>