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

if( empty( $_GET[ 'url' ] ) ) $_GET[ 'url' ] = '';

include LIB_DIR . "autoload.php";
include LIB_DIR . "base.php";

Benchmark::start( 'request', $_SERVER[ 'REQUEST_TIME' ] );

include LIB_DIR . "helpers.php";
include LIB_DIR . "errors/errors.php";
include LIB_DIR . "constants.php";

Config::instance() -> load();
Plugins::instance() -> loadManifests();

//Cache::pageCache( $_GET[ 'url' ] );
Security::instance();

if( ENV & ENV_HTTP ) {
	// Route
	Router::instance() -> route( $_GET[ 'url' ] );

	// Render
	View::instance() -> render( 'layouts', View::instance() -> layout );
}

if( ENV & ENV_TEST ) {
	include_once LIB_DIR . "scripts/scripts.php";
	array_shift( $argv );
	$scripts -> call( $argv );
}

Errors::show();

?>
