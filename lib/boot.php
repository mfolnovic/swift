<?php

session_start();

include LIB_DIR . "base.php";
//include LIB_DIR . "errors/errors.php";

include LIB_DIR . "constants.php";
include LIB_DIR . "helpers.php";
include LIB_DIR . "dir/dir.php";
include LIB_DIR . "router/router.php";

include LIB_DIR . "config/config.php";

include LIB_DIR . "log/log.php";
include LIB_DIR . "benchmark/benchmark.php";
include LIB_DIR . "image/image.php";
include LIB_DIR . "controller/base.php";
include LIB_DIR . "controller/controller.php";
include LIB_DIR . "cache/cache.php";
include LIB_DIR . "db/db.php";
include LIB_DIR . "model/base.php";
include LIB_DIR . "model/model.php";
include LIB_DIR . "view/view.php";

// Load config
$config -> load();

Cache::loadDrivers( $config -> options[ 'cache' ] );

if( ENV & ENV_HTTP ) {
	// With more features at unit tests, will move most of this to run at tests too
	
	// Initiate log
	$log -> init( "file", "application" );

	// Initiate cache
	// Initiate database
	$db -> init();

	// Route
	$router -> route( $_SERVER[ "REQUEST_URI" ] );

	// Render
	$view -> render( 'layouts', $view -> layout );
}

if( ENV & ENV_TEST ) {
	include_once LIB_DIR . "scripts/scripts.php";
	array_shift( $argv );
	$scripts -> call( $argv );
}

?>
