<?php

/**
 * Swift
 *
 * @package		Swift
 * @author		Swift dev team
 * @copyright	Copyright (c) 2010, Swift dev team
 * @license		LICENSE
 */

/**
 * Autoload function
 * @param		string	name	Name of class
 * @return	bool
 */

function __autoload( $name ) {
	$name = strtolower( $name );
	$path = str_replace( '_', '/', $name );
	if( strpos( $path, '/' ) === false ) $path .= '/' . $name;
	$path = LIB_DIR . $path . ".php";
	
	if( file_exists( $path ) )
		include $path;
	else if( !Plugins::instance() -> loadPlugin( $name ) )
		trigger_error( "Couldn't load class <i>$name</i>", ERROR );

	return true;
}

?>
