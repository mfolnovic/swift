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
 * Swift Controller Class
 *
 * This class controls running actions, and maintains security
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Controller
 */

class Controller extends Base {
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

	/**
	 * Runs a controller
	 *
	 * @access public
	 * @param  string $controller Name of controller
	 * @param  string $action     Name of action
	 * @param  array  $data       Contains data
	 * @return void
	 */
	public function run($controller, $action, $data = array()) {
		$this -> clean();

		try {
			App::load('controller', 'application', $controller);
		} catch(AppException $e) {
			Router::instance() -> continueRouting = TRUE;
		}

		$controllerName = $controller . 'Controller';

		if(is_callable(array($controllerName, $action))) {
			$this -> controller       = $controller;
			$this -> action           = $action;
			$this -> object           = new $controllerName;
			$this -> object -> data   = array_merge($_POST, $data);

			if(!file_exists(TMP_DIR . "caches/{$controller}_{$action}.php")) {
				$this -> object -> run_before_filters($action);
				$this -> object -> $action();
				$this -> object -> run_after_filters($action);
			}
		}
	}

	/**
	 * Renders 404
	 * @access public
	 * @return void
	 */
	public function render404() {
		ob_clean();
		include PUBLIC_DIR . "/404.html";
		exit;
	}

	/**
	 * Clean globals
	 * @access public
	 * @return void
	 */
	public function clean() {
		if(empty($this -> object)) {
			return;
		}

		foreach($this -> object -> globals as $key => $val) {
			unset($GLOBALS[$key]);
		}
	}
}

?>
