<?php

//echo $_SERVER['REQUEST_METHOD'];
session_start();

// Load constants
include LIB_DIR . "constants.php";
include LIB_DIR . "base.php";
include LIB_DIR . "helpers.php";
include LIB_DIR . "dir.php";
include LIB_DIR . "router.php";

include LIB_DIR . "config.php";
$config -> loadConfig();

include LIB_DIR . "log.php";
include LIB_DIR . "controller.php";
include LIB_DIR . "controllerBase.php";
include LIB_DIR . "cache/" . ( $config -> options[ 'cache' ][ 'driver' ] ) . ".php";
include LIB_DIR . "db/" . ( $config -> options[ 'database' ][ 'default' ][ 'driver' ] ) . ".php";
include LIB_DIR . "model.php";
include LIB_DIR . "view.php";

// Route
$router -> route( $_SERVER[ "REQUEST_URI" ] );

// Render

// TODO: make better way
if( $controller -> isAjax() )
	$view -> render();
else
	$view -> render( 'layouts', $view -> layout );

/*$users = new User() -> where( array( 'id' => 'bla' ) ); // Relation
$users = new User() -> all(); // Array
$user = new User() -> first(); // Row
$user -> validates();
$user -> username = 'bla';

foreach( $users -> all() as $id => $user ) // User => Row
	echo $user -> username;
	
$user = new User( array( 'username' => 'bla' ) ); // Row*/

?>
