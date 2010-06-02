<?php

// Load constants
include "constants.php";
include "base.php";
include "benchmark.php";

$benchmark -> start( "Whole request" );
$benchmark -> start( "Including library" );

// Load PRF
$files = array( "helpers", "log", "file", "controller", "controllerBase", "router", "config", "cache", "db", "model", "view" );
foreach( $files as $file ) 
	include LIB_DIR . $file . ".php";

function __autoload( $class ) {
	include_once MODEL_DIR . strtolower( $class ) . ".php";
}

$benchmark -> end( "Including library" );

// Route
$router -> route( $_SERVER[ "REQUEST_URI" ] );

// Render
$benchmark -> start( "Rendering" );
$view -> renderLayout();
$benchmark -> end( "Rendering" );
$benchmark -> end( "Whole request" );

?>
