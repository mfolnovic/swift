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
 * Swift Session Class
 *
 * Used for manipulating sessions (files, database)
 * Also, it can be used to set flash messages
 *
 * Flash messages are used for storing messages between requests. 
 * If you add flash message in current request, it'll stay in session
 * until end of the next request
 *
 * @author      Swift dev team
 * @package     Swift
 * @subpackage  Session
 */

class Session extends Base {
	/**
	 * TRUE if init was already run
	 */
	static $init = FALSE;

	/**
	 * Initialize session
	 * Handles flash messages between requests
	 *
	 * @access  public
	 * @return  void
	 * @static
	 */
	public static function init() {
		if(self::$init) {
			return;
		} else {
			self::$init = TRUE;
		}

		session_name('Swift'); // Application name?
		session_start();
		session_regenerate_id();

		if(!isset($_SESSION['flash'])) {
			$_SESSION['flash'] = array();
		}

		$id = self::increment('flash_id');
		foreach($_SESSION['flash'] as $index => &$value) {
			if($id - 1 > $value[0]) {
				unset($_SESSION['flash'][$index]);
			}
		}
	}

	/**
	 * Returns TRUE if session variable with index $index exists
	 *
	 * @access  public
	 * @param   mixed $index Index
	 * @return  bool
	 * @static
	 */
	public static function exists($index) {
		return isset($_SESSION[$index]);
	}

	/**
	 * Returns session variable with index $index
	 *
	 * @access  public
	 * @param   mixed $index Index
	 * @return  mixed
	 * @static
	 */
	public static function get($index) {
		if(!self::$init) {
			self::init();
		}

		return $_SESSION[$index];
	}

	/**
	 * Sets session variable with index $index to value $value
	 *
	 * @access  public
	 * @param   mixed $index Index
	 * @param   mixed $value Value
	 * @return  void
	 * @static
	 */
	public static function set($index, $value) {
		if(!self::$init) {
			self::init();
		}

		$_SESSION[$index] = $value;
	}

	/**
	 * Pushes $value to session variable (array) with index $index
	 *
	 * @access  public
	 * @param   mixed $index Index
	 * @param   mixed $value Value
	 * @return  void
	 * @static
	 */
	public static function push($index, $value) {
		if(!self::$init) {
			self::init();
		}

		$_SESSION[$index][] = $value;
	}

	/**
	 * Increments session variable with index $index
	 *
	 * @access  public
	 * @param   mixed $index Index
	 * @return  int
	 * @static
	 */
	public static function increment($index) {
		if(self::$init === FALSE) {
			self::init();
		}

		if(!isset($_SESSION[$index])) {
			$_SESSION[$index] = 0;
		}

		return ++ $_SESSION[$index];
	}

	/**
	 * Decrements session variable with index $index
	 *
	 * @access  public
	 * @param   mixed $index Index
	 * @return  int
	 * @static
	 */
	public static function decrement($index) {
		if(!self::$init) {
			self::init();
		}

		if(!isset($_SESSION[$index])) {
			$_SESSION[$index] = 0;
		}

		return -- $_SESSION[$index];
	}

	/**
	 * Gets flash message with index $index
	 *
	 * @access  public
	 * @param   string $index Index
	 * @return  mixed
	 * @static
	 */
	public static function flash_get($index) {
		if(!self::$init) {
			self::init();
		}

		if(isset($_SESSION['flash'][$index])) {
			return $_SESSION['flash'][$index][1];
		} else {
			return NULL;
		}
	}

	/**
	 * Sets flash message with index $index to value $value
	 *
	 * @access  public
	 * @param   mixed $index Index
	 * @param   mixed $value Value
	 * @return  void
	 * @static
	 */
	public static function flash_set($index, $value) {
		if(!self::$init) {
			self::init();
		}

		$_SESSION['flash'][$index] = array($_SESSION['flash_id'], $value + 1);
	}
}

?>
