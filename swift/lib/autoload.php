<?php

/**
 * Swift
 *
 * @author    Swift dev team
 * @copyright Copyright (c) 2010, Swift dev team
 * @license   LICENSE
 * @package   Swift
 */

/**
 * Autoload function
 * @param  string name Name of class
 * @return bool
 * @todo   use multiple autoload for plugins
 */

function __autoload( $name ) {
	$path = LIB_DIR . str_replace( '_', '/', $name ) . ".php";
	
	if( file_exists( $path ) )
		include $path;
	else if( !Plugins::instance() -> loadPlugin( $name ) )
		trigger_error( "Couldn't load class <i>$name</i>", ERROR );

	return true;
}

?>
