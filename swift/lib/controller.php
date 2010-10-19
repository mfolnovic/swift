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
 * Swift Controller Class - Base
 *
 * This class gives methods in all application's controllers
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Controller
 */

class Controller extends Base {
	/**
	 * Contains all before_filters
	 */
	var $before_filters = array();
	/**
	 * Contains all after_filters
	 */
	var $after_filters  = array();
	/**
	 * Contains all $_POST data
	 */
	var $data           = array();
	/**
	 * All vars available in views are here
	 */
	var $globals        = array();
	/**
	 * Name of controller
	 */
	var $controllerName = "";
	var $request;
	var $response;

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct($request, $response) {
		$this -> controllerName = strtolower(substr(get_class($this), 0, -10));
		$this -> csrf_token     = Security::instance() -> csrf_token;
		$this -> request        = $request;
		$this -> response       = $response;
	}

	/**
	 * Runs before_filters
	 *
	 * @access public
	 * @return void
	 */
	public function run_before_filters($action) {
		foreach($this -> before_filters as $function) {
			if(!isset($function[1]['only']) || in_array($action, $function[1]['only'])) {
				call_user_func(array($this, $function[0]));
			}
		}
	}

	/**
	 * Destructor
	 * Runs after_filters
	 *
	 * @access public
	 * @return void
	 */
	public function run_after_filters($action) {
		foreach($this -> after_filters as $function) {
			if(!isset($function[1]['only']) || in_array($action, $function[1]['only'])) {
				call_user_func(array($this, $function[0]));
			}
		}
	}

	/**
	 * Adds new before_filter
	 *
	 * @access public
	 * @param	 string $function1, ... Function which should be run as before_filter
	 * @return void
	 * @todo   Options as last argument?
	 * @todo   More DRY between before_filter and after_filter
	 */
	public function before_filter() {
		$functions = func_get_args();

		if(is_array(end($functions))) {
			$options = array_pop($functions);
		} else {
			$options = array();
		}

		foreach($functions as $function) {
			$this -> before_filters[] = array($function, $options);
		}
	}

	/**
	 * Adds new after filter
	 *
	 * @access public
	 * @param  string $function1, ... Function which should be run as after_filter
	 * @return void
	 * @todo   Options as last argument?
	 */
	public function after_filter($function) {
		$functions = func_get_args();
		if(is_array(end($functions))) {
			$options = array_pop($functions);
		} else {
			$options = array();
		}

		foreach($functions as $function) {
			$this -> after_filters[] = array($function, $options);
		}
	}

	/**
	 * Used for changing layout
	 *
	 * @access public
	 * @param  string $layout Name of new layout
	 * @return void
	 */
	public function layout($layout) {
		$this -> response -> layout = $layout;
	}

	/**
	 * Redirects to $url
	 *
	 * @access public
	 * @param  string $url Url to redirect to
	 * @return void
	*/
	public function redirect($url) {
		if(isAjax()) {
			header("X-Redirect: $url");
			$this -> request = new Request($url);
		}	else {
			header("Location:" . URL_PREFIX . $url);
			exit;
		}
	}

	/**
	 * Changes what it should be rendered
	 *
	 * @access public
	 * @param  string $url Path which should be rendered
	 * @return return
	 */
	public function render($path) {
		$this -> response -> render = $path;
	}

	/**
	 * Tells router that it missed action and should continue finding right one
	 *
	 * @access public
	 * @return void
	 */
	public function notFound() {
		App::$request -> render404();
	}

	/**
	 * Caches actions specified as arguments
	 *
	 * @access public
	 * @param  string $action,... Names of actions to cache
	 * @return return
	 */
	public function caches_action() {
		foreach(func_get_args() as $action) {
			$this -> response -> action_caches[] = array(&$this -> controllerName, $action);
		}
	}

	/**
	 * Gets global var with index $index
	 *
	 * @access public
	 * @param  mixed $index Index
	 * @return mixed
	 */
	public function &__get($index) {
		return $this -> globals[$index];
	}

	/**
	 * Sets var $index to value $value
	 *
	 * @access public
	 * @param  mixed $index Index
	 * @param  mixed $value Value
	 * @return object
	 */
	public function __set($index, $value) {
		$this -> globals[$index] = $value;

		return $this;
	}

	/**
	 * Tests if global var with index $index exists
	 *
	 * @access public
	 * @param  mixed $index Index
	 * @return object
	*/
	public function __isset($index) {
		return isset($this -> globals[$index]);
	}
	function action() {
		return $this -> request -> action;
	}
};

?>
