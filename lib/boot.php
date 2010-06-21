<?php

//echo $_SERVER['REQUEST_METHOD'];

// Load constants
include "constants.php";
include "base.php";
include "helpers.php";
include "log.php";
include "benchmark.php";

$benchmark -> start( "Whole request" );
$benchmark -> start( "Including library" );

// Load PRF
$files = array( "controller", "controllerBase", "router", "config", "cache", "db", "model", "view" );
foreach( $files as $file ) {
	$benchmark -> start( "Loading $file" );
	include LIB_DIR . $file . ".php";
	$benchmark -> end( "Loading $file" );
}

function __autoload( $class ) {
	include_once MODEL_DIR . strtolower( $class ) . ".php";
}

$benchmark -> end( "Including library" );

include MODEL_DIR . "vijest.php";
// Route
$router -> route( $_SERVER[ "REQUEST_URI" ] );

// Render
$benchmark -> start( "Rendering" );

// TODO: make better way
if( $controller -> isAjax() )
	$view -> render();
else
	$view -> render( 'layouts', $view -> layout );
	
$benchmark -> end( "Rendering" );
$benchmark -> end( "Whole request" );

$log -> log( "Memory usage: " . memory_get_peak_usage( TRUE ) / 1024 . " KB" );

/*$users = new User() -> where( array( 'id' => 'bla' ) ); // Relation
$users = new User() -> all(); // Array
$user = new User() -> first(); // Row
$user -> validates();
$user -> username = 'bla';

foreach( $users -> all() as $id => $user ) // User => Row
	echo $user -> username;
	
$user = new User( array( 'username' => 'bla' ) ); // Row*/

?>
