<?php 

// Set flag that this is a parent file
define( '_RMEXEC', 1 );

require_once ( 'include_files.php' );

class index {	
	
	public function execute() {
		
		log_service::enter_method(__CLASS__, __FUNCTION__);

		$result = null;

		if(isset($_GET["service"])) {
			$service = $_GET["service"];
			$database_reference = database_reference::create();
			
			$result = $database_reference->open();
			if(!isset($result)) {
				$database_model = abstract_factory::database_model($service);
				$database_object = abstract_factory::database_object($database_reference, 
					$database_model);
				
				$service_execution = new service_execution($database_object);
				
				$result = $service_execution->execute();

				$database_reference->close();
			}
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