<?php

class ControllerBase extends Base {
	var $data;

	function initObj() {
		global $controller;	
	
		$this -> data = &$controller -> data;
	}

	function layout( $layout ) {
		global $view;

		$view -> layout = $layout;
	}

	function redirect( $url ) {
		global $router;

		$router -> $route( $url );
	}

	function flash( $message ) {
		global $controller;	
	
		$controller -> flash[] = $message;
	}
	
	function model( $name, $data = array() ) {
		return new $name( $data );
	}

	function __get( $index ) {
		global $controller;
		
		return $controller -> globals[ $index ];
	}

 	function __set( $index, $value ) {
	 	global $controller;
 		
		$controller -> globals[ $index ] = $value;
	}
};

?>
