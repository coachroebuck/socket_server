<?php 

// Set flag that this is a parent file
define( '_RMEXEC', 1 );

require_once ( 'include_files.php' );

class index {	
	
	public function execute() {
		
		log_service::enter_method(__CLASS__, __FUNCTION__);

		$result = null;
		$api_results = array();
		$error_code = 200;

		if(!empty($_GET["service"])) {
			$service = $_GET["service"];
			$request_method = $_SERVER['REQUEST_METHOD'];
			$database_reference = database_reference::create();
			
			//More than likely, we'll be accessing the database
			if(!abstract_factory::can_skip_database_call($service, $request_method)) {
				$result = $database_reference->open();
				if(empty($result)) {
					$result = $database_reference->startTransaction();
				}

				if(empty($result)) {
					$affected_apis = abstract_factory::affected_database_tables($service);

					if(is_array($affected_apis)) {
						foreach($affected_apis as $key => $next_api) {
							$database_model = abstract_factory::database_model($next_api);
							$database_object = abstract_factory::database_object($database_reference, 
								$database_model);
							
							$service_execution = new service_execution($database_object);
							
							$nextResult = $service_execution->execute();

							//Stop if we encountered an error
							$error_code = log_service::error_code();
							if($error_code != 200) {
								http_response_code($error_code);
								$result = $nextResult;
								break;
							}
							else {
								$api_results[$next_api] = json_decode($nextResult, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
							}
						}
					}
				}

				//Only commit when the entire transaction (that is, an insert/update/delete transaction) has been successful.
				//If an error had occurred, the database object will have automatically reverted its changes.
				$error_code = log_service::error_code();
				if($error_code == 200) {
					if(strcmp($request_method, 'GET') != 0) {
						$database_reference->endTransaction();
					}
					$result = json_encode($api_results);
				}

				$database_reference->close();
			}

			//Post Actions
			if($error_code == 200) {
				$post_actions = abstract_factory::post_actions($service, $request_method);
				if(is_array($post_actions)) {
					foreach ($post_actions as $key => $value) {
						$object = new $value();
						$nextResult = $object->execute();
						$api_results[$value] = json_decode($nextResult);
					}

					if(sizeof($api_results) > 0) {
						$result = json_encode($api_results);
					}
				}
			}
		}
		else {
			$result = service_messaging::error("Service name required.");
		}

		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

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