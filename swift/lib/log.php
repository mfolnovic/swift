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
 * Swift Logger
 *
 * Maintains application log
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Log
 */

class Log extends Base {
	/**
	 * Instance of log adapter
	 */
	static $adapter;

	/**
	 * Init function
	 *
	 * @access public
	 * @return void
	 * @static
	 */
	static function init() {
		$options = Config::instance() -> get( 'log' );
		if( $options === FALSE ) return;

		$adapter = 'Log_' . $options[ 'adapter' ];
		self::$adapter = new $adapter( $options );

		self::write( '' );
		self::write( date( 'm.d.y H:m:s' ) . ' | ' . FULL_URL );
	}

	/**
	 * Destroy function
	 *
	 * @access public
	 * @return void
	 * @static
	 */
	static function destroy() {
		self::write( 'Request done in ' . Benchmark::end( 'request' ) . ' seconds' );
	}

	/**
	 * Writes to log
	 *
	 * @access public
	 * @param  string $message Message to write
	 * @return void
	 * @static
	 */
	static function write( $message, $type = NULL, $benchmark = NULL ) {
		if( self::$adapter === NULL ) self::init();

		if( !empty( $type ) ) $type = "[$type] ";
		if( !empty( $benchmark ) ) $benchmark = '(' . Benchmark::end( $benchmark ) . ' seconds)';

		self::$adapter -> write( "$type$benchmark" . ( !empty( $benchmark) || !empty( $type ) ? ': ' : '' ) . $message );
	}

	/**
	 * Write error to log
	 *
	 * @access public
	 * @param  string $message Message to write with flag error
	 * @return void
	 * @static
	 */
	static function error( $message ) {
		self::write( $message, ERROR );
	}

	/**
	 * Write notice to log
	 *
	 * @access public
	 * @param  string $message Message to write with flag notice
	 * @return void
	 * @static
	 */
	static function notice( $message ) {
		self::write( $message, NOTICE );
	}
}

register_shutdown_function( "Log::destroy" );

?>
