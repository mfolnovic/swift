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
 * Swift Logger
 *
 * Maintains application log
 *
 * @package			Swift
 * @subpackage	Log
 * @author			Swift dev team
 */

class Log extends Base {
	static $adapter;

	/**
	 * Init function
	 * @access	public
	 * @return	void
	 */
	static function init() {
		global $config;

		$options = $config -> options[ 'other' ][ 'log' ];
		if( $options === FALSE ) return;

		$adapter = 'Log_' . $options[ 'adapter' ];
		self::$adapter = new $adapter( $options );

		self::$adapter -> write( '' ); // empty line
		self::$adapter -> write( date( 'm.d.y H:m:s' ) . ' | ' . FULL_URL );
	}

	/**
	 * Destroy function
	 * @access	public
	 * @return	return
	 */
	static function destroy() {
		self::$adapter -> write( 'Request done in ' . Benchmark::end( 'request' ) . ' seconds', NULL );
	}

	/**
	 * Writes to log
	 * @access	public
	 * @param		string	message	Message to write
	 * @return	void
	 */
	static function write( $message, $type = NULL, $benchmark = NULL ) {
		if( self::$adapter === NULL ) self::init();

		if( !empty( $type ) ) $type = "[$type]";

		if( !empty( $benchmark ) ) $time = '(' . Benchmark::end( $benchmark ) . ' seconds)';
		else $time = '';

		self::$adapter -> write( "$type $time: $message" );
	}

	/**
	 * Write error to log
	 * @access	public
	 * @param		string	message	Message to write with flag error
	 * @return	void
	 */
	static function error( $message ) {
		self::$adapter -> write( $message, 'ERROR' );
	}

	/**
	 * Write notice to log
	 * @access	public
	 * @param		string	message	Message to write with flag notice
	 * @return	void
	 */
	static function notice( $message ) {
		self::$adapter -> write( $message, 'NOTICE' );
	}
}

?>
