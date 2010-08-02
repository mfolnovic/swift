<?php

/**
 * Autoload function
 * @param		string	name	Name of class
 * @return	bool
 */

function __autoload( $name ) {
	$name = strtolower( $name );
	$path = str_replace( '_', '/', $name );
	if( strpos( $path, '/' ) === false ) $path .= '/' . $name;

	include LIB_DIR . $path . ".php";
	return true;
}

?>
