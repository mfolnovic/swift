<?php

class Router extends Base {
	var $routes;

	function route( $path ) {
		global $controller, $benchmark;
		$benchmark -> start( "Routing" );
	
		$path = $this -> removePrefix( $path );	
		$path = explode( "/", $path );
		
		foreach( $this -> routes as $route ) {
			$r = $this -> checkRoute( $route, $path );
			if( $r != false ) {
				$benchmark -> end( "Routing" );
				$controller -> run( $r );
				return;
			}
		}
	}
	
	function checkRoute( $route, $path ) {
		$ret = array();
		
		$r = $route[ 'route' ];
		if( $path[ 0 ] == '' ) array_shift( $path );
		if( end( $path ) == '' ) array_pop( $path );
		
		$i = 0;
		foreach( $r as $id => $val ) {
			if( !isset( $path[ $i ] ) ) {
				if( $val[ 'optional' ] ) continue;
				else return false;
			} else if( $val[ 'var' ] == 1 )
				$ret[ $val[ 'name' ] ] = $path[ $i ];
			else if( $val[ 'name' ] != $path[ $i ] ) 
				return false;
				
			++ $i;
		}
				
		foreach( $route[ 'options' ] as $id => $val )
			$ret[ $id ] = $val;
				
		return $ret;
	}
	
	function parseRoute( $route, $options ) {
		$name = ''; $optional = 0;
		$ret = array();
				
		for( $i = 0, $len = strlen( $route ); $i < $len; ++ $i ) {
			if( $route[ $i ] == '/' ) {
				if( $name[ 0 ] == '%' && $name[ strlen( $name ) - 1 ] == '%' ) {
					$var = 1;
					$name = substr( $name, 1, -1 );
				} else $var = 0;
				
				$ret[] = array( 'name' => $name, 'optional' => $optional, 'var' => $var );
				$name = '';
			} else if( $route[ $i ] == '(' )
				++ $optional;
			else if( $route[ $i ] == ')' )
				-- $optional;
			else 
				$name .= $route[ $i ];
		}
		
		return array( 'route' => $ret, 'options' => $options );
	}

	function addRoute( $route, $options = array() ) {
		$this -> routes[] = $this -> parseRoute( $route, $options );
	}

	private function removePrefix( $path ) {
		$str = $_SERVER[ "SCRIPT_NAME" ];
		for( $i = 0, $len = strlen( $str ), $len2 = strlen( $path ); $i < $len && $i < $len2; ++ $i )
			if( $str[ $i ] != $path[ $i ] )
				return substr( $path, $i );

		return '';
	}
}

$router = new Router;

?>
