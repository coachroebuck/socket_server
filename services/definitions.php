<?php
// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

// set the default timezone to use. Available since PHP 5.1
date_default_timezone_set('UTC');

//set the path
define('RMPATH_BASE', dirname(__FILE__) );

//set the directory separator
define( 'DS', DIRECTORY_SEPARATOR );

define( 'API_DIRECTORY', "api");
define( 'BIN_DIRECTORY', "bin");
define('DATABASE_DIRECTORY', 'database');

$isLocalHost = strpos($_SERVER['HTTP_HOST'], 'localhost') > -1
	|| strpos($_SERVER['HTTP_HOST'], '192.168') > -1;

define('DB_SERVER', ( $isLocalHost ? '127.0.0.1' : '<my domain>'));
define('DB_NAME', 'lang2lang');
define('DB_USER', ( $isLocalHost ? 'root' : '<my user name>'));
define('DB_PASSWORD', ( $isLocalHost ? 'root' : '<my password>'));

//********************************************************************
define('OAUTH_CLIENT_ID', 'lang2lang_client');
define('OAUTH_CLIENT_SECRET', '8B7C2C31C9DB56B74A8D5216777A1');

define('APPLICATION_TRACE', 0);

define('UC_REGISTER_AUTOLOGIN', 1);
define('UC_REGISTER_WELCOME_EMAIL', 1);

?>