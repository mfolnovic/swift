<?php

class Controller extends Base {
	var $data, $obj;
	var $globals = array(), $flash = array();
	
	function run( $r ) {
		$this -> controller = $controller = $r[ 'controller' ];
		$this -> action = $action = $r[ 'action' ];
		
		unset( $r[ 'controller' ], $r[ 'action' ] );
		$this -> data = $r;

		$path = CONTROLLERS_DIR . $controller . ".php";
		if( file_exists( $path ) )
			include_once $path;
		else
			$this -> render404();

		if( is_callable( array( $tmp = $controller . 'Controller', $action ) ) ) {
			$this -> obj = new $tmp;
			$this -> obj -> initObj();
			$this -> obj -> $action();
		}
	}
	
	private function call2( $controller, $action ) {		
	}
	
	private function get( $controller ) {
	}
	
	function isAjax() {
		return isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) === 'xmlhttprequest';
	}
	
	function render404() {
		ob_clean();
		include PUBLIC_DIR . "/404.html";
		exit;
	}
}

$controller = new Controller;

?>
