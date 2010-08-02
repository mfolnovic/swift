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
	var $data = array();
	var $controller = NULL;
	var $action = NULL;
	var $instance = NULL;
	var $csrf_token;

	/**
	 * Constructor
	 * @access	public
	 * @return	void
	 */
	function __construct() {
		if( !$this -> checkCSRF() ) $this -> render404();

		$this -> csrf_token = md5( mt_rand() );
//		Cache::getInstance( 'default' ) -> set( 'csrf_token_' . $this -> csrf_token, 1, 3600 );
	}

	/**
	 * Runs a controller
	 * @access	public
	 * @param		string	$controller	Name of controller
	 * @param		string	$action			Name of action
	 * @return	void
	 */
	function run( $controller, $action ) {
		global $router;
		$this -> data = array_merge( $this -> filterXSS( $_POST ), $this -> data );

		include_once CONTROLLERS_DIR . "application.php"; // loading ApplicationController

		$path = CONTROLLERS_DIR . $controller . ".php";
		if( file_exists( $path ) )
			include_once $path;
		else {
			$router -> continueRouting = true;
			return;
		}

		$controllerName = $controller . 'Controller';
		
		if( is_callable( array( $controllerName, $action ) ) ) {
			$this -> instance = new $controllerName;
			$this -> controller = $controller;
			$this -> action = $action;
			$this -> instance -> data = & $this -> data; // workaround
			if( !file_exists( TMP_DIR . "caches/{$controller}_{$action}.php" ) )
				$this -> instance -> $action();
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
	 * Checks if CSRF token is correct
	 * @access	public
	 * @return	void
	 * @todo		Move it to new file which handles security!
	 */
	function checkCSRF() {
		return empty( $_POST ) || Cache::getInstance( 'default' ) -> exists( 'csrf_token_' . $_POST[ 'csrf_token' ] );
	}

	/**
	 * Fitlers array $array from XSS
	 * @access	public
	 * @param		array	$array	Array to filter
	 * @return	array
	 * @todo		Move it to new file which handles security!
	 */
	function filterXSS( $array ) {
		if( is_string( $array ) ) return htmlentities( $array );
		
		foreach( $array as $id => &$val )
			if( is_array( $val ) ) 
				$val = $this -> filterXSS( $val );
			else if( is_string( $val ) )
				$val = htmlentities( $val );
				
		return $array;
	}
}

$controller = new Controller;

?>
