<?php

class Controller extends Base {
	/**
	 * Array containing $_POST + URL data
	*/
	var $data;
	/**
	 * Controller name
	*/
	var $controller;
	/**
	 * Action name
	*/
	var $action;
	
	/**
	 * Runs a controller
	 * @param array $r Array passed from router, parsed url in array, e.g. /users/show/1 => array( 'controller => 'users', 'action' => 'show', 'id' => 1 ) ( default route )
	*/
	function run( $r ) {
		$this -> controller = $controller = $r[ 'controller' ];
		$this -> action = $action = $r[ 'action' ];
		
		unset( $r[ 'controller' ], $r[ 'action' ] );
		$this -> data = array_merge( $_POST, $r );

		$path = CONTROLLERS_DIR . $controller . ".php";
		if( file_exists( $path ) )
			include_once $path;
		else
			$this -> render404();

		$controllerName = $controller . 'Controller';
		if( is_callable( array( $controllerName, $action ) ) ) {
			$obj = new $controllerName;
			$obj -> data = & $this -> data;
			$obj -> $action();
		}
	}
	
	/** 
	 * Tests if ajax is active
	*/
	function isAjax() {
		return isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) === 'xmlhttprequest';
	}
	
	/**
	 * Render 404 
	*/
	function render404() {
		ob_clean();
		include PUBLIC_DIR . "/404.html";
		exit;
	}
}

$controller = new Controller;

?>
