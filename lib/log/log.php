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
	static $instance = NULL;

	/**
	 * Constructor
	 * @access	public
	 * @return	void
	 * @todo		Create adapters, like cache and db
	 */
	function __construct() {
		global $config;

		$options = $config -> options[ 'other' ][ 'log' ];
		if( $options === FALSE ) return;

		$adapter = 'Log_' . $options[ 'adapter' ];
		$this -> adapter = new $adapter( $options );
	}

	/**
	 * Singleton
	 * @access	public
	 * @static
	 * @return	object
	 */
	static function getInstance() {
		if( self::$instance == NULL ) self::$instance = new Log();
		return self::$instance;
	}

	/**
	 * Writes to log
	 * @access	public
	 * @param		string	message	Message to write
	 * @return	void
	 * @todo Support multiple adapters
	 */
	function write( $message ) {
		if( $this -> adapter === NULL ) return;
		$this -> adapter -> write( $message );
	}

	/**
	 * Write error to log
	 * @access	public
	 * @param		string	message	Message to write with flag error
	 * @return	void
	 */
	function error( $message ) {
		if( $this -> adapter === NULL ) return;
		$this -> adapter -> write( "[ERROR] $message" );
	}

	/**
	 * Write notice to log
	 * @access	public
	 * @param		string	message	Message to write with flag notice
	 * @return	void
	 */
	function notice( $message ) {
		if( $this -> adapter === NULL ) return;
		$this -> adapter -> write( "[NOTICE] $message" );
	}
}

?>
