<?php

class Controller extends Base {
	var $data;
	
	function run( $r ) {
		$this -> data = $r;
		
		$this -> get( $r[ 'controller' ] );
		$this -> call( $r[ 'controller' ], $r[ 'action' ] );
	}
	
	private function call( $controller, $action ) {		
		if( is_callable( array( $controller . 'Controller', $action ) ) ) {
			$str = $controller . 'Controller';
			$obj = new $str;
			$obj -> initObj();
			$obj -> $action();
		} else
			die( "Action not found!" );
	}
	
	private function get( $controller ) {
		$path = CONTROLLERS_DIR . $controller . ".php";
		if( file_exists( $path ) )
			include $path;
		else
			die( "Controller not found" ); // need exceptions...
	}
}

$controller = new Controller;

?>
