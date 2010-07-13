<?php

class ControllerBase extends Base {
	/**
	 * Array containing data from $container -> data
	*/
	var $data;
	var $before_filter = array();
	var $config;
	
	function __construct() {
		global $config;
	
		// running before filers
		foreach( $this -> before_filter as $func )
			call_user_func( array( $this, $func ) );

		$this -> config = & $config -> options;
	}

	/**
	 * Used for changing layout
	 * @param string $layout Name of layout to change to
	*/
	function layout( $layout ) {
		global $view;

		$view -> layout = $layout;
	}

	/**
	 * Redirects to $url
	 * @param string $url Url to redirect to
	*/
	function redirect( $url ) {
		global $router, $controller, $config;
		
		header( "X-Redirect: " . ( isAjax() ? '' : $config -> options[ 'other' ][ 'url_prefix' ] ) . "$url" );
		$this -> data = array();
		$router -> route( $url, false );
		$controller -> run();
	}
	
	function render( $url ) {
		global $router;

		$router -> route( $url, false );
	}

	/**
	 * Returns instance of model $name, and can also create new row from data $data
	 * @param string $name Name of model
	 * @param array $data New row
	*/
	function model( $name, $data = array() ) {
		include_once MODEL_DIR . strtolower( $name ) . ".php";		
		return new $name( $data );
	}
	
	function controller() { 
		global $controller; 
		return $controller -> controller; 
	}
	
	function action() {
		global $controller;
		return $controller -> action;
	}
	
	function notFound() {
		global $router;
		$router -> continueRouting = true;
	}

	function &__get( $index ) {
		global $controller;
		
		return $controller -> globals[ $index ];
	}

 	function __set( $index, $value ) {
	 	global $controller;
 		
		$controller -> globals[ $index ] = $value;
	}
	
	function __isset( $index ) {
		global $controller;
	
		return isset( $controller -> globals[ $index ] );
	}
};

?>
