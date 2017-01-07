<?php

// Set flag that this is a parent file
defined('_RMEXEC') or define( '_RMEXEC', 1 );

class server
{
	//************************************************************************************	
	static public function get()
	{
		$dsn      = 'mysql:dbname=' . DB_NAME . ';host=' . DB_SERVER;
		$username = DB_USER;
		$password = DB_PASSWORD;
		
		// error reporting (this is a demo, after all!)
		ini_set('display_errors',1);error_reporting(E_ALL);
		
		// Autoloading (composer is preferred, but for this example let's just do this)
		require_once(RMPATH_BASE . DS . BIN_DIRECTORY . DS . 'oauth2-server-php'.DS.'src'.DS.'OAuth2'.DS.'Autoloader.php');
		OAuth2\Autoloader::register();
		
		// $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
		$storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
		
		// Pass a storage object or array of storage objects to the OAuth2 server class
		//do not issue a new refresh token upon successful token request
		//tokens last 1 year from time of issue
		$access_lifetime = 60 * 60 * 24 * 365;
		$refresh_token_lifetime = 60 * 60 * 24 * 365 * 2;
		$server = new OAuth2\Server($storage, array(
			'always_issue_new_refresh_token' => true,
            'id_lifetime'              => $access_lifetime,
            'access_lifetime'          => $access_lifetime,
			'refresh_token_lifetime'         => $refresh_token_lifetime,
		));
		
		// Add the "Client Credentials" grant type (it is the simplest of the grant types)
		$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
		
		// Add the "Authorization Code" grant type (this is where the oauth magic happens)
		$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
		$server->addGrantType(new OAuth2\GrantType\RefreshToken($storage));
		$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
		$server->addGrantType(new OAuth2\GrantType\UserCredentials($storage));
		
		return $server;
	}
}

?>