<?php

// Load constants
include "constants.php";

// Load PRF
$files = array( "helpers", "base", "log", "controller", "controllerBase", "router", "config", "db", "model", "view" );
foreach( $files as $file ) 
	include LIB_DIR . $file . ".php";

function __autoload( $class ) {
	include_once MODEL_DIR . strtolower( $class ) . ".php";
}

// Route
$router -> route( $_SERVER[ "REQUEST_URI" ] );

// Render
$view -> renderLayout();

?>
