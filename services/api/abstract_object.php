<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class abstract_object {

	protected function init() {

	}

	protected function deinit($object) {
		$properties = get_object_vars($object);
		foreach($properties as $nextProperty) {
			if(isset($nextProperty)) {
				unset($nextProperty);
			}
		}
	}

	protected function getJsonMessage($key, $value) {
		$array = array($key => $value);
		$result = json_encode($array);
		unset($array);
		return $result;
	}

	protected function buildQueryComponents($object, &$fields, &$conditions, $alias) {
		
		$array = get_object_vars($object);

		$fields = " SELECT ";
		foreach($array as $key => $value) {
			$fields .= " $alias.$key, ";

			if(isset($value)) {
				self::appendConditionsText($conditions, $key, $value, $alias);
			}
			else if(isset($_GET[$key])) {
				self::appendConditionsText($conditions, $key, $_GET[$key], $alias);
			}
		}

		$fields = rtrim($fields, ", ");
	}

	private function appendConditionsText(&$input, $key, $value, $alias) {
		if(!isset($input) || strlen($input) == 0) {

			if(is_string($value)) {
				$input = " WHERE $alias.$key = '" . addslashes($value) . "' ";
			}
			else {
				$input = " WHERE $alias.$key = $value ";
			}
		}
		else {
			if(is_string($value)) {
				$input .= " AND $alias.$key = '" . addslashes($value) . "' ";
			}
			else {
				$input .= " AND $alias.$key = $value ";
			}
		}
	}
}
?>