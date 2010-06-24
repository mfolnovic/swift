<?php

//echo $_SERVER['REQUEST_METHOD'];

session_start();

// Load constants
include "constants.php";
include "base.php";
include "helpers.php";
include "log.php";
include "dir.php";
include "controller.php";
include "controllerBase.php";
include "router.php";

include "config.php";
$config -> loadConfig();

include "cache.php";
include "db.php";
include "model.php";
include "view.php";

function __autoload( $class ) {
	include_once MODEL_DIR . strtolower( $class ) . ".php";
}

include MODEL_DIR . "vijest.php";
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
