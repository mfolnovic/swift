<?php

class Router extends Base {
	var $routes, $path;

	function route( $path ) {
		global $controller;
		
		if( ( $pos = strpos( $path, "?" ) ) !== FALSE ) $path = substr( $path, 0, $pos );
	
		$this -> path = explode( "/", $this -> removePrefix( $path ) );	
		if( $this -> path[ 0 ] == '' ) array_shift( $this -> path );
		if( end( $this -> path ) == '' ) array_pop( $this -> path );
		
		foreach( $this -> path as &$value ) 
			$value = str_replace( "+", " ", $value );
			
		foreach( $this -> routes as $route ) {
			$r = $this -> checkRoute( $route, $this -> path );
			if( $r != false ) {
				$controller -> run( $r );
				return;
			}
		}
		
		$controller -> render404();
	}
	
	function checkRoute( $route, $path ) {
		$ret = array();
		
		$r = $route[ 'route' ];
				
		$i = 0;
		
		foreach( $r as $id => $val ) {
			if( !isset( $path[ $i ] ) || ( $val[ 'name' ] != $path[ $i ] && $val[ 'var' ] == 0 ) ) {
				if( $val[ 'optional' ] ) continue;
				else return false;
			} else if( $val[ 'var' ] == 1 )
				$ret[ $val[ 'name' ] ] = $path[ $i ];
			
			++ $i;
		}
		
		foreach( $route[ 'options' ] as $id => $val )
			if( !isset( $ret[ $id ] ) )
				$ret[ $id ] = $val;

		return $ret;
	}
	
	function parseRoute( $route, $options ) {
		$name = ''; $optional = 0;
		$ret = array();
		
		for( $i = 0, $len = strlen( $route ); $i < $len; ++ $i ) {
			if( $route[ $i ] == '/' || $route[ $i ] == ')' ) {
				if( !empty( $name ) && $name[ 0 ] == '%' && $name[ strlen( $name ) - 1 ] == '%' ) {
					$var = 1;
					$name = substr( $name, 1, -1 );
				} else $var = 0;
				
				$ret[] = array( 'name' => $name, 'optional' => $optional, 'var' => $var );
				if( $route[ $i ] == ')' ) -- $optional;
				$name = '';
			} else if( $route[ $i ] == '(' )
				++ $optional;
			else 
				$name .= $route[ $i ];
		}

		return array( 'route' => $ret, 'options' => $options );
	}

	function addRoute( $route, $options = array() ) {
		$this -> routes[] = $this -> parseRoute( $route, $options );
	}
	
	function root( $controller, $action ) {
		$this -> routes[] = array( 'route' => array(), 'options' => array( 'controller' => $controller, 'action' => $action ) );
	}
	
	function resource( $name ) {
/*		$this -> routes[] = $this -> parseRoute( "$name/", array( 'action' => 'index' ) );
		$this -> routes[] = $this -> parseRoute( "$name/%id%", array( 'action' => 'view' ) );
		$this -> routes[] = $this -> parseRoute( "$name/%id%/edit", array( 'action' => 'edit' ) );*/
		
		$this -> routes[] = $this -> parseRoute( "$name/(%action%/(%id%))", array( 'controller' => $name, 'action' => 'index' ) );
	}

	private function removePrefix( $path ) {
		$str = $_SERVER[ "SCRIPT_NAME" ];

		for( $i = 0, $len = strlen( $str ), $len2 = strlen( $path ); $i < $len && $i < $len2; ++ $i )
			if( $str[ $i ] != $path[ $i ] ) {
				if( !defined( "URL_PREFIX" ) )
					define( "URL_PREFIX", substr( $path, 0, $i - 1 ) );
				return substr( $path, $i );
			}
			
		if( !defined( "URL_PREFIX" ) )
			define( "URL_PREFIX", substr( $str, 0, -10 ) ); // remove index.php
		return '';
	}
}

$router = new Router;

?>
