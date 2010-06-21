<?php

class Controller extends Base {
	var $data, $obj;
	var $globals = array(), $flash = array();
	
	function run( $r ) {
		global $benchmark;
		
		$benchmark -> start( "Running controller" );
		$this -> controller = $r[ 'controller' ];
		$this -> action = $r[ 'action' ];
		
		unset( $r[ 'controller' ], $r[ 'action' ] );
		$this -> data = $r;

		$this -> get( $this -> controller );
		$this -> call( $this -> controller, $this -> action );
		
		$benchmark -> end( "Running controller" );
	}
	
	private function call( $controller, $action ) {		
		if( is_callable( array( $controller . 'Controller', $action ) ) ) {
			$str = $controller . 'Controller';
			$this -> obj = new $str;
			$this -> obj -> initObj();
			$this -> obj -> $action();
		}
	}
	
	private function get( $controller ) {
		$path = CONTROLLERS_DIR . $controller . ".php";
		if( file_exists( $path ) )
			include $path;
		else
			$this -> render404();
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
