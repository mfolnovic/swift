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

class Request extends Base {
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
	 * Use this route for empty url (/)
	 */
	var $root;
	/**
	 * Current controller
	 */
	var $controller = NULL;
	/**
	 * Current action
	 */
	var $action     = NULL;
	/**
	 * Current controller instance
	 */
	var $object     = NULL;
	var $code       = 200;
	var $messages   = array(
		100 => "Continue",
		101 => "Switching Protocols",
		102 => "Processing",
		200 => "OK",
		201 => "Created",
		202 => "Accepted",
		203 => "Non-Authoritative Information",
		204 => "No Content",
		205 => "Reset Content",
		206 => "Partial Content",
		207 => "Multi-Status",
		300 => "Multiple Choices",
		301 => "Moved Permanently",
		302 => "Found",
		303 => "See Other",
		304 => "Not Modified",
		305 => "Use Proxy",
		306 => "Switch Proxy",
		307 => "Temporary Redirect",
		400 => "Bad Request",
		401 => "Unauthorized",
		402 => "Payment Required",
		403 => "Forbidden",
		404 => "Not Found",
		405 => "Method Not Allowed",
		406 => "Not Acceptable",
		407 => "Proxy Authentication Required",
		408 => "Request Timeout",
		409 => "Conflict",
		410 => "Gone",
		411 => "Length Required",
		412 => "Precondition Failed",
		413 => "Request Entity Too Large",
		414 => "Request-URI Too Long",
		415 => "Unsupported Media Type",
		416 => "Requested Range Not Satisfiable",
		417 => "Expectation Failed",
		418 => "I'm a teapot",
		422 => "Unprocessable Entity",
		423 => "Locked",
		424 => "Failed Dependency",
		425 => "Unordered Collection",
		426 => "Upgrade Required",
		449 => "Retry With",
		450 => "Blocked by Windows Parental Controls",
		500 => "Internal Server Error",
		501 => "Not Implemented",
		502 => "Bad Gateway",
		503 => "Service Unavailable",
		504 => "Gateway Timeout",
		505 => "HTTP Version Not Supported",
		506 => "Variant Also Negotiates",
		507 => "Insufficient Storage",
		509 => "Bandwidth Limit Exceeded",
		510 => "Not Extended"
	);

	/**
	 * Main function responsible to route $path to current controller & action
	 *
	 * @access public
	 * @param  string $path Path to route from
	 * @return void
	 * @todo   Move including routes to constructor
	 */
	public function route($url) {
		if(empty($this -> routes)) {
			include CONFIG_DIR . 'routes.php';
		}

		$this -> path = $url;

		if(empty($this -> path)) {
			$this -> runController($this -> root['controller'], $this -> root['action']);
			return;
		}

		$this -> url  = trim(strtr($this -> path, "+", " "), '/');
		$this -> path = preg_split("/[\/\.]/", $this -> url);

		foreach($this -> routes as $route) {
			if($this -> checkRoute($route, $this -> path)) {
				return;
			}
		}

		$this -> setStatus(404);
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
		$i   = 0;
		$ret = array();

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

		return $this -> runController($ret['controller'], $ret['action'], $ret);
	}

	/**
	 * Parses route $route with options $options and adds it to other routes
	 *
	 * @access public
	 * @param  string $route   Route to parse and add
	 * @param  array  $options Options for this route
	 * @return void
	 */
	public function addRoute($route, $options = array()) {
		$ret   = array();
		$route = explode('/', $route);

		foreach($route as $id => $val)
			if($val[0] == ':') {
				$ret[] = array(substr($val, 1), true);
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

	/**
	 * Runs a controller
	 *
	 * @access public
	 * @param  string $controller Name of controller
	 * @param  string $action     Name of action
	 * @param  array  $data       Contains data ($_POST, $_GET)
	 * @return void
	 */
	public function runController($controller, $action, $data = array()) {
		$this -> clean();

		try {
			App::load('controller', 'application', $controller);
		} catch(AppException $e) {
			return false;
		}

		$this -> controller = $controller;
		$this -> action     = $action;

		$controllerName = $controller . 'Controller';
		$actionName     = $action;

		if(method_exists($controllerName, $actionName)) {
			$this -> object           = new $controllerName($this, App::$response, array_merge($_POST, $data));

			if(!file_exists(TMP_DIR . "caches/{$controller}_{$action}.php")) {
				$this -> object -> run_before_filters($actionName);
				$this -> object -> $actionName();
				$this -> object -> run_after_filters($actionName);
			}

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Sets header status code to $code
	 *
	 * @access public
	 * @param  int $code Code
	 * @return void
	 */
	public function setStatus($code) {
		$this -> code = $code;
	}

	/**
	 * Clean globals
	 *
	 * @access public
	 * @private
	 * @return void
	 */
	private function clean() {
		if(empty($this -> object)) {
			return;
		}

		foreach($this -> object -> globals as $key => $val) {
			unset($GLOBALS[$key]);
		}
	}
}

?>
