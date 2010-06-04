<?php

// Load constants
include "constants.php";
include "base.php";
include "benchmark.php";

$benchmark -> start( "Whole request" );
$benchmark -> start( "Including library" );

// Load PRF
$files = array( "helpers", "log", "controller", "controllerBase", "router", "config", "cache", "db", "model", "view" );
foreach( $files as $file ) 
	include LIB_DIR . $file . ".php";

function __autoload( $class ) {
	include_once MODEL_DIR . strtolower( $class ) . ".php";
}

$benchmark -> end( "Including library" );

include MODEL_DIR . "user.php";
// Route
$router -> route( $_SERVER[ "REQUEST_URI" ] );

// Render
$benchmark -> start( "Rendering" );
$view -> renderLayout();
$benchmark -> end( "Rendering" );
$benchmark -> end( "Whole request" );

/*$users = new User() -> where( array( 'id' => 'bla' ) ); // Relation
$users = new User() -> all(); // Array
$user = new User() -> first(); // Row
$user -> validates();
$user -> username = 'bla';

foreach( $users -> all() as $id => $user ) // User => Row
	echo $user -> username;
	
$user = new User( array( 'username' => 'bla' ) ); // Row*/

?>
