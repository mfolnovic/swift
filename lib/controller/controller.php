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
	var $globals = array();
	/**
	 * Runs a controller
	 * @param array $r Array passed from router, parsed url in array, e.g. /users/show/1 => array( 'controller => 'users', 'action' => 'show', 'id' => 1 ) ( default route )
	*/
	function run() {
		$this -> data = array_merge( $_POST, $this -> data );

		$path = CONTROLLERS_DIR . $this -> controller . ".php";
		if( file_exists( $path ) )
			include_once $path;
		else
			$this -> render404();

		$controllerName = $this -> controller . 'Controller';
		$actionName = $this -> action;
		if( is_callable( array( $controllerName, $this -> action ) ) ) {
			$obj = new $controllerName;
			$obj -> data = & $this -> data; // workaround
			$obj -> $actionName();
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
