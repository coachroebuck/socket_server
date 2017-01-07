<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class abstract_factory {

	static public function pre_actions($name) {

		$array = array(
			"account" => array(
			),
		);
		
		if(array_key_exists($name, $array)) {
			$requests = $array[$name];			

			if(array_key_exists($server_request, $requests)) {
				return $requests[$server_request];			
			}
		}

		return null;

		return null;
	}

	static public function post_actions($service, $request_method) {

		$array = array(
			"account" => array(
				"POST" => array(
					"login",
				),
			),
			"login" => array(
				"POST" => array(
					"login",
				),
			),
			"logout" => array(
				"POST" => array(
					"logout",
				),
			),
		);
		
		if(array_key_exists($service, $array)) {
			$requests = $array[$service];			

			if(array_key_exists($request_method, $requests)) {
				log_service::log("service=$service request_method=$request_method requests=[" 
					. isset($requests[$request_method]) . "]");
				return $requests[$request_method];			
			}
		}

		return null;
	}

	static public function can_skip_database_call($service, $request_method) {

		$array = array(
			"login" => array(
				"POST" => true,
			),
			"logout" => array(
				"POST" => true,
			),
		);
		
		if(array_key_exists($service, $array)) {
			$ignore_states = $array[$service];			

			if(array_key_exists($request_method, $ignore_states)) {
				log_service::log("service=$service request_method=$request_method ignore states=[" 
					. $ignore_states[$request_method] . "]");
				return $ignore_states[$request_method];			
			}
		}

		return null;
	}

	static public function affected_database_tables($name) {

		$array = array("account" => array("user", "profile"));
		
		if(array_key_exists($name, $array)) {
			return $array[$name];
		}

		return array($name);
	}

	static public function database_object($db, $model) {
		$obj = new abstract_db_table_object();
		$obj->initialize($db, $model);
		return $obj;
	}

	static public function database_model($name) {
		
		log_service::enter_method(__CLASS__, __FUNCTION__);

		$result = null;

		$obj = "l2l_" . $name . "_model";
		if(class_exists($obj)) {
			$result = new $obj();
		}
		$obj = "oauth_" . $name . "_model";
		if(class_exists($obj)) {
			$result = new $obj();
		}

		log_service::exit_method(__CLASS__, __FUNCTION__, "database_model=[$obj]");

		return $result;
	}
}
?>