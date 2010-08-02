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
 * Swift Directory Class
 *
 * Gives internal directory manipulation to Swift
 *
 * @package			Swift
 * @subpackage	Directory
 * @author			Swift dev team
 */

class Dir {
	/**
	 * Returns all files in $dir
	 * @access	public
	 * @param		string	dir	Directory to search in
	 * @return	array
	 */
	function files( $dir ) {
		$ret = array();
		$dir = scandir( $dir );

		foreach( $dir as $a )
			if( $a[ 0 ] != '.' && $a[ strlen( $a ) - 1 ] != '/' )
				$ret[] = $a;

		return $ret;
	}

	/**
	 * Reads from file
	 * @access	public
	 * @deprecated
	 * @param		string	string	directory
	 * @param		string	file		file
	 * @return	string
	 */
	function read( $dir, $file ) {
		return file_get_contents( $dir . '/' . $file );
	}
}

$dir = new Dir;

?>
