<?php

// Load constants
include "constants.php";

loadConfig();
includeLib();

// Load config files
function loadConfig() {
	$files = array( "application" );

	foreach( $files as $file )	
		include CONFIG_DIR . $file . ".php";
}


// Load PRF files
function includeLib() {
	$files = array( "base", "log" );

	foreach( $files as $file )
		include LIB_DIR . $file . ".php";
}

?>
