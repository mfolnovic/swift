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
		global $router, $controller;
		
//		$controller -> headers[ 'X-Redirect' ] = $url;
		header( "X-Redirect: $url" );
		$router -> route( $url );
	}

	function flash( $message ) {
		global $controller;	
	
		$controller -> flash[] = $message;
	}
	
	function model( $name, $data = array() ) {
		include_once MODEL_DIR . strtolower( $name ) . ".php";		
		return new $name( $data );
	}

	function &__get( $index ) {
		global $controller;
		
		return $controller -> globals[ $index ];
	}

 	function __set( $index, $value ) {
	 	global $controller;
 		
		$controller -> globals[ $index ] = $value;
	}
};

?>
