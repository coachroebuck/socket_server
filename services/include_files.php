<?php

// no direct access
defined( '_RMEXEC' ) or die( 'Restricted access' );

require_once ( 'definitions.php' );

function include_files($dirname, $recursiveSearch = true)
{
	foreach (scandir($dirname) as $filename) {
	    $path = $dirname . $filename;
	    if(is_dir($path) && strrpos($path, '.') != strlen($path) - 1 && $recursiveSearch)
	    {
	    	include_files($path . DS);
	    }
	    else if (is_file($path)) {
	    	$pathinfo = pathinfo($path);
	    	if(strcmp($pathinfo['extension'], "php") == 0) {
		        require_once($path);
	    	}
	    }
	}
}

include_files(dirname(__FILE__).DS.API_DIRECTORY.DS);

?>