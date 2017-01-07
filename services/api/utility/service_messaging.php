<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class service_messaging {
	
	static public function error($message) {
		return service_messaging::create("error", $message);
	}

	static public function info($message) {
		return service_messaging::create("info", $message);
	}

	static public function verbose($message) {
		return service_messaging::create("verbose", $message);
	}

	static public function debug($message) {
		return service_messaging::create("debug", $message);
	}

	static public function warn($message) {
		return service_messaging::create("warning", $message);
	}

	static public function create($key, $value) {
		
		$arr = array($key => $value);
		$result = json_encode($arr);
		unset($arr);
		return $result;
	}
}

?>