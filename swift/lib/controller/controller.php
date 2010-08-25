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
 * Swift Controller Class
 *
 * This class controls running actions, and maintains security
 *
 * @package			Swift
 * @subpackage	Controller
 * @author			Swift dev team
 */

class Controller extends Base {
	var $controller = NULL;
	var $action = NULL;
	var $object = NULL;
	var $csrf_token;
	static $instance = NULL;

	/**
	 * Runs a controller
	 * @access	public
	 * @param		string	controller	Name of controller
	 * @param		string	action			Name of action
	 * @param		array		data				Contains data
	 * @return	void
	 * @todo		Move filterXSS to somewhere else, since now, it'll be run more times
	 */
	function run( $controller, $action, $data = array() ) {
		global $router;

		$this -> clean();
		include_once CONTROLLERS_DIR . "application.php"; // loading ApplicationController

		$path = CONTROLLERS_DIR . $controller . ".php";
		if( file_exists( $path ) )
			include_once $path;
		else {
			Router::instance() -> continueRouting = true;
			return;
		}

		$controllerName = $controller . 'Controller';

		if( is_callable( array( $controllerName, $action ) ) ) {
			$this -> object         = new $controllerName;
			$this -> controller       = $controller;
			$this -> action           = $action;
			$this -> object -> data = array_merge( $_POST, $data );

			if( !file_exists( TMP_DIR . "caches/{$controller}_{$action}.php" ) )
				$this -> object -> $action();
		}
	}

	/**
	 * Renders 404
	 * @access	public
	 * @return	void
	 */
	function render404() {
		ob_clean();
		include PUBLIC_DIR . "/404.html";
		exit;
	}

	/**
	 * Clean globals
	 * @access	public
	 * @return	void
	 */
	function clean() {
		if( empty( $this -> object ) ) return;
		foreach( $this -> object -> globals as $key => $val )
			unset( $GLOBALS[ $key ] );
	}

	/**
	 * Singleton
	 * @access	public
	 * @return	object
	 */
	static function instance() {
		if( empty( self::$instance ) ) self::$instance = new Controller;
		return self::$instance;
	}
}

$controller = new Controller;

?>
