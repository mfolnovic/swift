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
 * Swift Error Handler
 *
 * Maintains all errors
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Errors
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
	 */
	public static function error($exception) {
			self::$errors[] = array('number' => $exception -> getCode(), 'message' => $exception -> getMessage(), 'file' => $exception -> getFile(), 'line' => $exception -> getLine(), 'backtrace' => $exception -> getTrace(), 'type' => 'error');

			LOG::write($exception -> getMessage() . ' | ' . $exception -> getFile() . ' | ' . $exception -> getLine(), 'error');

/*			if($number === E_USER_ERROR || $number == E_ERROR) {
				self::show();
			}
		}*/
	}

	/**
	 * Shows error page
	 *
	 * @access public
	 * @return return
	 * @static
	 */
	static function show() {
		if(empty(self::$errors)) {
			return;
		}
		@ob_clean();
		header("HTTP/1.1 500 Internal Server Error");

		$errors = self::$errors;
		include PUBLIC_DIR . "500.php";
		exit;
	}

	function exception($number, $message, $filename, $line) {
		throw new ErrorException($message, 0, $number, $filename, $line); 
	}
}

set_error_handler(array('Errors', 'exception'));
set_exception_handler(array('Errors', 'error'));
register_shutdown_function(array('Errors', 'show'));

?>
