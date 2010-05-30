<?php

// Load constants
include "constants.php";


// Load PRF
$files = array( "helpers", "base", "log", "router", "config" );
foreach( $files as $file ) 
	include LIB_DIR . $file . ".php";

// Route
$router -> route( $_SERVER[ "REQUEST_URI" ] );

?>
