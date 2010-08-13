<?php

/**
 * Swift framework
 *
 * @package		Swift
 * @author		Swift dev team
 * @copyright	Copyright (c) 2010, Swift dev team
 * @license		LICENSE
 */

/**
 * Swift Benchmark Class
 *
 * This class allows you to measure time needed for certain
 * part of code to run
 *
 * @package			Swift
 * @subpackage	Benchmark
 * @author			Swift dev team
 */

class Benchmark extends Base {
	static $times = array();
	static $id = 0;

	/**
	 * This function marks start point for name $name
	 * @access	public
	 * @static
	 * @param		string	$name	Mark name
	 * @return	void
	 */
	static function start( $name = NULL, $time = NULL ) {
		if( empty( $name ) ) $name = ++ self::$id;
		self::$times[ $name ] = $time ? $time : microtime( true );
		return $name;
	}

	/**
	 * This function calculates difference between time from name $name
	 * to current time and returns it
	 * @access	public
	 * @static
	 * @param		string	$name	Mark name
	 * @return	void
	 */
	static function end( $name ) {
		if( !isset( self::$times[ $name ] ) ) return 0;

		return round( ( microtime( true ) - self::$times[ $name ] ), 4 );
	}
}

?>
