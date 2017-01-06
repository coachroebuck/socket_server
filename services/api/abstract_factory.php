<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class abstract_factory {

	static public function database_object($name, $db, $table_name, $model) {
		$obj = new abstract_db_object();
		$obj->initialize($db, $table_name, $model);
		return $obj;
	}

	static public function database_table_name($name) {
		return "l2l_" . $name;
	}

	static public function database_model($name) {
		$obj = "l2l_" . $name . "_model";
		if(class_exists($obj)) {
			return new $obj();
		}
		return null;
	}
}
?>