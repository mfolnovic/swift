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
	static $adapter = NULL;

	/**
	 * Init function
	 *
	 * @access public
	 * @return void
	 * @static
	 */
	public static function init() {
		$options = Config::get('log');
		if($options === FALSE) return;

		$adapter = 'Log_' . $options['adapter'];
		self::$adapter = new $adapter($options);

		self::write('');
		self::write(date('m.d.y H:m:s') . ' | ' . FULL_URL);
	}

	/**
	 * Destroy function
	 *
	 * @access public
	 * @return void
	 * @static
	 */
	public static function destroy() {
		Benchmark::start('request', $_SERVER['REQUEST_TIME']);
		self::write('Request done in ' . Benchmark::end('request') . ' seconds');
	}

	/**
	 * Writes to log
	 *
	 * @access public
	 * @param  string $message Message to write
	 * @return void
	 * @static
	 */
	public static function write($message, $type = NULL, $benchmark = NULL) {
		if(empty(self::$adapter)) {
			self::init();
		}

		if(self::$adapter === NULL) return;

		if(!empty($type)) $type = "[$type] ";
		if(!empty($benchmark)) $benchmark = '(' . Benchmark::end($benchmark) . ' seconds)';

		self::$adapter -> write("$type$benchmark" . (!empty($benchmark) || !empty($type) ? ': ' : '') . $message);
	}

	/**
	 * Write error to log
	 *
	 * @access public
	 * @param  string $message Message to write with flag error
	 * @return void
	 * @static
	 */
	public static function error($message) {
		self::write($message, ERROR);
	}

	/**
	 * Write notice to log
	 *
	 * @access public
	 * @param  string $message Message to write with flag notice
	 * @return void
	 * @static
	 */
	public static function notice($message) {
		self::write($message, NOTICE);
	}
}

register_shutdown_function("Log::destroy");

?>
