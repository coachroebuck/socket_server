<?php
// Set flag that this is a parent file
define( '_RMEXEC', 1 );

//set the path
define('RMPATH_BASE', dirname(__FILE__) );

//set the directory separator
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( RMPATH_BASE.DS.'definitions.php' );

// define( 'SERVICES_DIRECTORY', "api".DS."v".API_VERSION);
// define( 'DB_OBJECTS_DIRECTORY', "api".DS."v".API_VERSION .DS."db");
// define( 'CONFIG_DIRECTORY', "api".DS."v".API_VERSION.DS."config");
// define( 'PHOTOS_DIRECTORY', "photos");
// define( 'BIN_DIRECTORY', "bin");

// require_once ( RMPATH_BASE.DS.'bin'.DS.'GoogleMapsGeocoder.php' );

// function include_files($dirname, $recursiveSearch = true)
// {
// 	foreach (scandir($dirname) as $filename) {
// 	    $path = $dirname . $filename;
// 	    if (is_file($path)) {
// 	    	$pathinfo = pathinfo($path);
// 	    	if(strcmp($pathinfo['extension'], "php") == 0 && strcmp($pathinfo['filename'], "index") != 0) {
// 		        require_once($path);
// 	    	}
// 	    }
// 	    else if(is_dir($path) && strrpos($path, '.') != strlen($path) - 1 && $recursiveSearch)
// 	    {
// 	    	include_files($path . DS);
// 	    }
// 	}
// }

// include_files(dirname(__FILE__).DS.SERVICES_DIRECTORY.DS);
// include_files(dirname(__FILE__).DS.DB_OBJECTS_DIRECTORY.DS);
// include_files(dirname(__FILE__).DS.CONFIG_DIRECTORY.DS);
// include_files(dirname(__FILE__).DS.BIN_DIRECTORY.DS, false);
?>