<?php

/**
 * Swift framework
 *
 * @author     Swift dev team
 * @copyright  Copyright (c) 2010, Swift dev team
 * @license    LICENSE
 * @package    Swift
 */

/**
 * Swift Benchmark Class
 *
 * This class allows you to measure time needed for certain
 * part of code to run
 *
 * @author      Swift dev team
 * @package     Swift
 * @subpackage  Benchmark
 */

class Benchmark extends Base {
	/**
	 * Storage for all mark points and their times
	 */
	static $marks = array();
	/**
	 * Internal ID
	 * It is used for marks that don't really need specific name
	*/
	static $id = 0;

	/**
	 * This function marks start point with name $name
	 *
	 * @access public
	 * @param  string $name Mark name
	 * @static
	 * @return string
	 */
	static function start( $name = NULL, $time = NULL ) {
		if( empty( $name ) ) $name = ++ self::$id;
		if( empty( $time ) ) $time = microtime( true );

		self::$marks[ $name ] = $time;
		return $name;
	}

	/**
	 * This function calculates difference between time from mark $name
	 * to current time and returns it
	 *
	 * @access public
	 * @param  string $name  Mark name
	 * @param  int    $round Number of decimal digits to round to
	 * @static
	 * @return int
	 */
	static function end( $name, $round = 4 ) {
		if( !isset( self::$marks[ $name ] ) ) {
			trigger_error( "Benchmark mark with name $name doesn't exist.", WARNING ); 
			return 0;
		}

		return round( ( microtime( true ) - self::$marks[ $name ] ), $round );
	}
}

?>
