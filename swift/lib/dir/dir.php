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
	static function files( $dir ) {
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
	static function read( $dir, $file ) {
		return file_get_contents( $dir . '/' . $file );
	}

	/**
	 * Create directory $dir if it doesn't exist
	 * @access	public
	 * @param		dir	string	Directory
	 * @return	return
	 */
	static function make_dir( $dir ) {
		if( file_exists( $dir ) ) return;
		$dir = dirname( $dir );
		$dirs = explode( '/', $dir );
		$current = '';

		foreach( $dirs as $value ) {
			$current .= $value . '/';
			if( !file_exists( $current ) )
				if( @mkdir( $current ) === FALSE )
					trigger_error( "Couldn't make directory $current" );
		}
	}
}

?>