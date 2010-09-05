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
 * Swift Error Handler
 *
 * Maintains all errors
 *
 * @package			Swift
 * @subpackage	Errors
 * @author			Swift dev team
 */

class Errors extends Base {
	/**
	 * Contains all errors
	*/
	static $errors = array();
	/**
	 * Adds error to $errors
	 *
	 * @access public
	 * @param  int    number  Error number
	 * @param  string message Error message
	 * @param  string file    In file $file
	 * @param  int    line    In line $line
	 * @return void
	 * @static
	 * @todo   Avoid switch
	 */
	static function error( $number, $message, $file, $line ) {
		if( error_reporting() ) {
			switch( $number ) {
				case E_USER_NOTICE;
				case E_NOTICE;
					$type = 'notice';
				break;
				case E_USER_WARNING;
				case E_WARNING;
					$type = 'warning';
				break;
				case E_USER_ERROR:
				case E_NOTICE;
					$type = 'error';
				break;
				default:
					$type = 'notice';
			}

			self::$errors[] = array( 'number' => $number, 'message' => $message, 'file' => $file, 'line' => $line, 'backtrace' => array_slice( debug_backtrace(), 1 ), 'type' => $type );
			LOG::write( $message . ' | ' . $file . ' | ' . $line, $type );
			if( $number === E_USER_ERROR || $number == E_ERROR ) self::show();
		}
	}

	/**
	 * Shows error page
	 *
	 * @access public
	 * @return return
	 * @static
	 */
	static function show() {
		if( empty( self::$errors ) ) return;
		@ob_clean();
		header( "HTTP/1.1 500 Internal Server Error" );

		$errors = self::$errors;
		include PUBLIC_DIR . "500.php";
		exit;
	}
}

set_error_handler( array( 'Errors', 'error' ) );

?>
