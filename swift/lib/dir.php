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
 * Swift Directory Class
 *
 * Gives internal directory manipulation to Swift
 *
 * @author      Swift dev team
 * @package     Swift
 * @subpackage  Directory
 */

class Dir {
	/**
	 * Returns all files in directory $path
	 *
	 * @access public
	 * @param  string $path Directory to search in
	 * @return array
	 * @static
	 * @todo   Almost same function as dirs, try to merge them?
	 */
	static function files( $path ) {
		$ret = array();
		$dir = scandir( $path );

		foreach( $dir as $a )
			if( $a[ 0 ] != '.' && is_file( $path . $a ) )
				$ret[] = $a;

		return $ret;
	}

	/**
	 * Returns all directories in directory $path
	 *
	 * @access public
	 * @param  string $path Directory to search in
	 * @return array
	 * @static
	 * @todo   Rename to directories?
	 */
	static function dirs( $path ) {
		$ret = array();
		$dir = scandir( $path );

		foreach( $dir as $a )
			if( $a[ 0 ] != '.' && is_dir( $path . $a ) )
				$ret[] = $a;

		return $ret;
	}

	/**
	 * Returns all files and directories in directory $path
	 *
	 * @access public
	 * @param  string $path Directory to search in
	 * @static
	 * @return array
	 */
	static function all( $path ) {
		return array_slice( scandir( $path ), 2 );
	}

	/**
	 * Reads from file
	 *
	 * @access public
	 * @deprecated
	 * @param  string $dir  directory
	 * @param  string $file file
	 * @return string
	 * @static
	 * @todo   Move to class file
	 */
	static function read( $dir, $file ) {
		return file_get_contents( $dir . '/' . $file );
	}

	/**
	 * Create directory $dir if it doesn't exist
	 *
	 * @access public
	 * @param  dir $string Directory
	 * @return void
	 * @static
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

	/**
	 * Returns part of both path where both paths have same directories
	 * @access  public
	 * @param   string $path1 First path
	 * @param   string $path2 Second path
	 * @return  return
	 * @static
	 */
	static function sameFolders( $path1, $path2 ) {
		$ret = '';
		$tmp = '';

		for( $i = 0, $size = min( strlen( $path1 ), strlen( $path2 ) ); $i < $size; ++ $i ) {
			if( $path1[ $i ] != $path2[ $i ] ) break;
			else if( $path1[ $i ] == '/' ) {
				$ret .= $tmp;
				$tmp  = '/';
			} else
				$tmp .= $path1[ $i ];
		}

		return $ret . '/';
	}
}

?>
