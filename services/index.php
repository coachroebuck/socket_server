<?php 

// Set flag that this is a parent file
define( '_RMEXEC', 1 );

require_once ( 'include_files.php' );

class index {	
	
	public function execute() {
		
		log_service::enter_method(__CLASS__, __FUNCTION__);

		$result = null;

		if(!empty($_GET["service"])) {
			$service = $_GET["service"];
			$database_reference = database_reference::create();
			$bypass = false;

			if(strcmp($_SERVER['REQUEST_METHOD'], 'PUT') != 0 
				&& strcmp($service, "user") == 0) {
				$bypass = true;
				echo "<h1>Creating new account</h1>";
				exit;
			}

			$result = $database_reference->open();
			if(empty($result)) {
				$result = $database_reference->startTransaction();
			}
			if(empty($result)) {
				//TODO: I have a problem. This API only performs one action.
				//This must change.
				//Currently, the service_execution object contains a single abstract_database_table_object.
				//That abstract_database_table_object contains a reference to the database file handle and a abstract_database_model.
				$database_model = abstract_factory::database_model($service);
				$database_object = abstract_factory::database_object($database_reference, 
					$database_model);
				
				$service_execution = new service_execution($database_object);
				
				$result = $service_execution->execute();

				//Stop if we encountered an error
				$error_code = log_service::error_code();
				if($error_code != 200) {
					http_response_code($error_code);
				}
			}

			//Only commit when the entire transaction (that is, an insert/update/delete transaction) has been successful.
			//If an error had occurred, the database object will have automatically reverted its changes.
			$error_code = log_service::error_code();
			if($error_code == 200 
				&& strcmp($_SERVER['REQUEST_METHOD'], 'GET') != 0) {
				$database_reference->endTransaction();
			}

			$database_reference->close();
		}
		else {
			$result = service_messaging::error("Service name required.");
		}

		log_service::exit_method(__CLASS__, __FUNCTION__);

		return $result;
	}
}


log_service::log("------------------------------------------------------------------------------------");
$index = new index();
$result = $index->execute();
header("content-type:text/html; charset=utf-8"); 
echo $result;
unset($result);
unset($index);
log_service::log("------------------------------------------------------------------------------------\n\n");

?>