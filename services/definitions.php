<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

// set the default timezone to use. Available since PHP 5.1
date_default_timezone_set('UTC');

//Global definitions
//Joomla framework path definitions
$parts = explode( DS, RMPATH_BASE );

define('APP_TRACE_ENABLED', 1);

//********************************************************************
//This is database login information...
$isLocalHost = strpos($_SERVER['HTTP_HOST'], 'localhost') > -1
	|| strpos($_SERVER['HTTP_HOST'], '192.168') > -1;
define('DB_SERVER', ( $isLocalHost ? '127.0.0.1' : '<my IP address'));
define('DB_NAME', '<my database name>');
define('DB_USER', ( $isLocalHost ? 'root' : '<my username>'));
define('DB_PASSWORD', ( $isLocalHost ? 'root' : '<my password>'));

//********************************************************************
define('UC_CLIENT_ID', '<my OAuth 2.0 Client ID>');
define('UC_CLIENT_SECRET', '<my OAuth 2.0 Secret>');

//TODO: Put this stuff in a database!!
define('DEFAULT_FIRST_RECORD', '0');
define('DEFAULT_TOTAL_RECORDS', '1000');
define('DEFAULT_PASSWORD_RECOVERY_DURATION', 'INTERVAL 30 MINUTE');
define('MINIMUM_PASSWORD_CHARACTERS', 8);
define('MINIMUM_USERNAME_CHARACTERS', 8);
define('MAXIMUM_LOG_FILE', 60 * 60 * 24 * 7);
define('MAXIMUM_EMAIL_CHARACTERS', 190);
define('MAXIMUM_SHORTFIELD_CHARACTERS', 255);
define('MAXIMUM_DESCRIPTION_CHARACTERS', 10000);
define('MAXIMUM_UPLOAD_SIZE', "5M");

define('TABLE_ALIAS_PLACEHOLDER', "##");
define('TABLE_COLUMNNAME_PLACEHOLDER', "@@");

define('CURRENT_TIMESTAMP_KEYWORD', "CURRENT_TIMESTAMP");
define('UNIX_TIMESTAMP_KEYWORD', "UNIX_TIMESTAMP");

if(isset($_GET["v"])) {
	$appVersion = $_GET["v"];
	define('API_VERSION', $appVersion);
}
else {
	define('API_VERSION', '0.1');	
}

define('ENABLE_ADDRESS_VALIDATION_VIA_GOOGLE', 0);

//********************************************************************
//Any new object that is to be created: add the definitions here:
//	RMPATH_<ACTUAL OBJECT NAME>
//	RMTAG_<OBJECT NAME>
//	RMHEADERTITLE_<OBJECT NAME>
//Then the profile.php (or whatever routing name I gave it) will need modified
//	I tried to make this simple.
//********************************************************************

//these are paths to the objects
define( 'RMPATH_ROOT',			implode( DS, $parts ) );
define( 'RMPATH_SITE',				RMPATH_ROOT );

//These are required parameter keys from the query string / postback
define('UC_METHOD', 'method');
define('UC_VALUE', 'value');
define('UC_OBJECT', 'object');
define('UC_ACCESS_TOKEN', 'access_token');
define('UC_REGISTER_AUTOLOGIN', 1);
define('UC_REGISTER_WELCOME_EMAIL', 1);
define('UC_REGISTRATION_DEFAULT_STATUS', 1);

//********************************************************************
//Everything else helps drives the backend.
//The definitions consists of 
//	action names,
//	error codes
//	keyword url parameters
//	default values, 
//	etc
//********************************************************************  
define('UC_EVENT_HISTORY_DATE_INTERVAL', 'UNIX_TIMESTAMP(CURDATE() - INTERVAL 1 DAY)');
define('UC_NO_REPLY_EMAIL_ADDRESS', 'no-reply@<domain name>');

?>