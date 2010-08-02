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

	/**
	 * This function marks start point for name $name
	 * @access	public
	 * @static
	 * @param		string	Mark name
	 * @return	void
	 */
	static function start( $name ) {
		self::$times[ $name ] = microtime( true );
	}

	/**
	 * This function calculates difference between time from name $name
	 * to current time and writes it to log.
	 * @access	public
	 * @static
	 * @param		string	Mark name
	 * @return	void
	 */
	static function end( $name ) {
		Log::getInstance() -> write( "[Benchmark] $name: " . round( ( microtime( true ) - self::$times[ $name ] ), 4 ) . " seconds!" );
	}
}

?>
