<?php

// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

require_once ( 'definitions.php' );

function include_files($dirname, $recursiveSearch = true)
{
	$directories = array();
	foreach (scandir($dirname) as $filename) {
	    $path = $dirname . $filename;
	    if(is_dir($path) && strrpos($path, '.') != strlen($path) - 1 && $recursiveSearch)
	    {
	    	array_push($directories, $path . DS);
	    }
	    else if (is_file($path)) {
	    	$pathinfo = pathinfo($path);
	    	if(strcmp($pathinfo['extension'], "php") == 0) {
		        require_once($path);
	    	}
	    }

	    foreach ($directories as $key => $value) {
	    	include_files($value);
	    }
	}
}

include_files(dirname(__FILE__).DS.API_DIRECTORY.DS);

?>