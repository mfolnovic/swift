<?php

class Controller extends Base {
	/**
	 * Array containing $_POST + URL data
	*/
	var $data = array();
	/**
	 * Controller name
	*/
	var $controller = NULL;
	/**
	 * Action name
	*/
	var $action = NULL;
	var $instance = NULL;
	
	/**
	 * Runs a controller
	 * @param array $r Array passed from router, parsed url in array, e.g. /users/show/1 => array( 'controller => 'users', 'action' => 'show', 'id' => 1 ) ( default route )
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
			$this -> instance -> $action();
		}
	}
	
	/**
	 * Render 404 
	*/
	function render404() {
		ob_clean();
		include PUBLIC_DIR . "/404.html";
		exit;
	}
	
	function checkCSRF() {
		return empty( $_POST ) || Cache::getInstance( 'default' ) -> exists( 'csrf_token_' . $_POST[ 'csrf_token' ] );
	}
	
	function filterXSS( $array ) {
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
