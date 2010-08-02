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
 * @subpackage	Subpackage
 * @author			Swift dev team
 */

class Errors extends Base {
	/**
	 * description
	 * @access	public
	 * @param		int			number	Error number
	 * @param		string	message	Error message
	 * @param		string	file		In file $file
	 * @param		int			line		In line $line
	 * @return	void
	 */
	function error( $number, $message, $file, $line ) {
		if( error_reporting() ) {
			ob_clean();
			$backtrace = debug_backtrace();

			include PUBLIC_DIR . "500.php";

			exit;
		}
	}
}

set_error_handler( array( 'Errors', 'error' ) );

?>
