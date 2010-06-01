<?php

class Controller extends Base {
	var $data;
	
	function run( $r ) {
		global $benchmark;
		
		$benchmark -> start( "Running controller" );
		$this -> controller = $r[ 'controller' ];
		$this -> action = $r[ 'action' ];

		$this -> data = array_diff_key( $r, array( 'controller' => '', 'action' => '' ) ); // workaround, any better way?

		$this -> get( $this -> controller );
		$this -> call( $this -> controller, $this -> action );
		
		$benchmark -> end( "Running controller" );
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
