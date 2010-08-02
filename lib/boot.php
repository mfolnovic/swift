<?php

/**
 * Swift
 *
 * @package		Swift
 * @author		Swift dev team
 * @copyright	Copyright (c) 2010, Swift dev team
 * @license		LICENSE
 */

session_start();

include LIB_DIR . "base.php";
include LIB_DIR . "errors/errors.php";
include LIB_DIR . "autoload.php";

include LIB_DIR . "constants.php";
include LIB_DIR . "helpers.php";
include LIB_DIR . "dir/dir.php";
include LIB_DIR . "router/router.php";

include LIB_DIR . "config/config.php";

include LIB_DIR . "controller/controller.php";
include LIB_DIR . "db/db.php";
include LIB_DIR . "view/view.php";

// Load config
$config -> load();

if( ENV & ENV_HTTP ) {
	// With more features at unit tests, will move most of this to run at tests too

	// Route
	$router -> route( $_SERVER[ "REQUEST_URI" ] );

	// Render
	View::getInstance() -> render( 'layouts', View::getInstance() -> layout );
}

if( ENV & ENV_TEST ) {
	include_once LIB_DIR . "scripts/scripts.php";
	array_shift( $argv );
	$scripts -> call( $argv );
}

?>
