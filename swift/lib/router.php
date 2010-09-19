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
 * Swift Router
 *
 * Routes all requests to correct controller and action
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Router
 */

class Router extends Base {
	/**
	 * Contains all routes
	 */
	var $routes;
	/**
	 * Current path
	 */
	var $path;
	/**
	 * Root route
	 * Use this route for empty url
	 */
	var $root;
	/**
	 * @internal
	 *
	 * If this one is true, then try to find other route
	 * Signal from controller that Router missed correct route
	*/
	var $continueRouting;

	/**
	 * Constructor
	 *
	 * @access public
	 * @param  string name description
	 * @return return
	 */
	public function __construct() {
		$this -> loadRoutes();
	}

	/**
	 * Main function responsible to route $path to current controller & action
	 *
	 * @access public
	 * @param  string $path Path to route from
	 * @return void
	 * @todo   Allows URLs like %controller%/%action%/%h%:%m%:%s%
	 */
	public function route($path) {
		if(empty($path)) {
			Controller::instance() -> run($this -> root['controller'], $this -> root['action']);
			return;
		}

		$path = str_replace("+", " ", $path);
		$start = 0; 
		$end = strpos($path, "?") - 1;
		if($end == -1) $end = strlen($path) - 1;

		while($start <= $end) {
			if($path[$start] == '/') ++ $start;
			else if($path[$end] == '/') -- $end;
			else break;
		}

		$path = substr($path, $start, $end - $start + 1);

		$this -> url = $path;
		$this -> path = explode("/", $path);

		foreach($this -> routes as $route)
			if($this -> checkRoute($route, $this -> path))
				return;

		Controller::instance() -> render404();
	}

	/**
	 * Checks if route $route is correct for $path
	 *
	 * @access public
	 * @param  array $route Route to check for
	 * @param  array $path  Current path
	 * @return return
	 */
	public function checkRoute(&$route, $path) {
		$i                       = 0;
		$ret                     = array();
		$this -> continueRouting = false;

		foreach($route[0] as $val) {
			if(!isset($path[$i])) {
				if($val[1] === false) {
					return false;
				}

				break;
			}

			if($val[1] === false && $val[0] != $path[$i]) {
				return false;
			} else if($val[1] === true) {
				$ret[$val[0]] = $path[$i];
			}

			++ $i;
		}

		foreach($route[1] as $id => $val) {
			if(!isset($ret[$id])) {
				$ret[$id] = $val;
			}
		}

		Controller::instance() -> run($ret['controller'], $ret['action'], $ret);

		return !$this -> continueRouting;
	}

	/**
	 * Load all routes from routes.php
	 */
	public function loadRoutes() {
		include CONFIG_DIR . "routes.php";
	}

	/**
	 * Parses route $route with options $options and adds it to other routes
	 *
	 * @access public
	 * @param  string $route   Route to parse and add
	 * @param  array  $options Options for this route
	 * @return void
	 * @todo   Do I really need to parse all routes?
	 */
	public function addRoute($route, $options = array()) {
		$ret   = array();
		$route = explode('/', $route);

		foreach($route as $id => $val)
			if($val[0] == '%') {
				$ret[] = array(substr($val, 1, -1), true);
			} else {
				$ret[] = array($val, false);
			}

		$this -> routes[] = array($ret, $options);
	}

	/**
	 * Creates root route, for empty url
	 * @access public
	 * @param  string $controller Root controller
	 * @param  string $action     Root action
	 * @return void
	 */
	public function root($controller, $action) {
		$this -> root = array('controller' => $controller, 'action' => $action);
	}
}

?>
