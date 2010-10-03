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
 * Swift Base Class
 *
 * Each class is inherited by this one
 * Provides before_filter and after_filter (rails-like)
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Base
 */

class Base {
	/**
	 * Contains all singleton instances
	*/
	static $instance  = array();
	var    $pinstance = array();

	/**
	 * Searches through all plugins and finds that function
	 *
	 * @access public
	 * @param  string $name Function name
	 * @param  array  $args Arguments
	 * @return void
	 */
	public function __call($name, $args) {
		$plugins = Plugins::instance();
		$classes = get_parent_classes(get_class($this));

		foreach($classes as $class) {
			if(!isset($plugins -> extends[$class])) {
				continue;
			}

			foreach((array)$plugins -> extends[$class] as $class_name) {
				if(method_exists($class_name, $name)) {
					if(!isset($this -> pinstance[$class_name])) {
						$this -> pinstance[$class_name] = new $class_name($this);
					}

					call_user_func_array(array($this -> pinstance[$class_name], $name), $args);
					return true;
				}
			}
		}

		throw new Exception( "Unknown function $name" );

		return false;
	}

	/**
	 * Same as above, but for static calls
	 *
	 * @access public
	 * @param  string $name Function name
	 * @param  array  $args Arguments
	 * @return void
	 */
	public static function __callStatic($name, $args) {
//		call_user_func_array(array(__CLASS__, $name), $args);
	}

	/**
	 * Global singleton
	 *
	 * @access public
	 * @param  string $args Optional arguments passed to constructor
	 * @return object
	 */
	public static function instance($args = NULL) {
		$name = get_called_class();

		if(!isset(self::$instance[$name])) {
			self::$instance[$name] = new $name($args);
		}

		return self::$instance[$name];
	}
}

?>
