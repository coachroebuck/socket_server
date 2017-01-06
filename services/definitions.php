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

define('APP_TRACE_ENABLED', 1);

$isLocalHost = strpos($_SERVER['HTTP_HOST'], 'localhost') > -1
	|| strpos($_SERVER['HTTP_HOST'], '192.168') > -1;

define('DB_SERVER', ( $isLocalHost ? '127.0.0.1' : '<my domain>'));
define('DB_NAME', 'lang2lang');
define('DB_USER', ( $isLocalHost ? 'root' : '<my user name'));
define('DB_PASSWORD', ( $isLocalHost ? 'root' : '<my password>'));

//********************************************************************
define('OAUTH_CLIENT_ID', '<my client ID>');
define('OAUTH_CLIENT_SECRET', '<my client secret>');

define('APPLICATION_TRACE', 1);

define('DATABASE_NAME', 'lang2lang');

define('UC_REGISTER_AUTOLOGIN', 1);
define('UC_REGISTER_WELCOME_EMAIL', 1);
define('UC_REGISTRATION_DEFAULT_STATUS', 1);

?>