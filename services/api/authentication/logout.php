<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

class logout {

	public function execute() {

		log_service::enter_method(__CLASS__, __FUNCTION__);

		ob_start();

		$method = $_SERVER['REQUEST_METHOD'];
		$result = null;

		switch ($method) {
			case 'POST':
				$server = server::get();
				$result = $server->handleRevokeRequest(OAuth2\Request::createFromGlobals())->send();
				break;
			
			default:
				$result = "request method [$method] has not been implemented.";
				break;
		}

		$result = ob_get_contents();

		ob_end_clean();

		log_service::exit_method(__CLASS__, __FUNCTION__, $result);

		return $result;
	}
}
?>