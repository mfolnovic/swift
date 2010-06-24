<?php

class Router extends Base {
	var $routes, $path, $root;

	function route( $path ) {
		global $controller;
		
		$path = str_replace( "+", " ", $path );
		$start = strlen( URL_PREFIX );
		$end = strpos( $path, "?" ) - 1;
		if( $end == -1 ) $end = strlen( $path ) - 1;

		while( $start < $end ) {
			if( $path[ $start ] == '/' ) ++ $start;
			else if( $path[ $end ] == '/' ) -- $end;
			else break;
		}
		
		$path = substr( $path, $start, $end - $start + 1 );		

		if( empty( $path ) ) {
			$controller -> run( $this -> root );
			return;
		}
		
		$this -> path = explode( "/", $path );	

		foreach( $this -> routes as $route )
			if( $this -> checkRouteAndRun( $route, $this -> path ) )
				return;

		$controller -> render404();
	}
	
	function checkRouteAndRun( $route, $path ) {
		global $controller;
		$ret = array();
		
		$i = 0;
		foreach( $route[ 0 ] as $val ) {
			if( !isset( $path[ $i ] ) ) break;
			
			if( $val[ 1 ] === false && $val[ 0 ] != $path[ $i ] )
				return false;
			else if( $val[ 1 ] === true )
				$ret[ $val[ 0 ] ] = $path[ $i ];
			
			++ $i;
		}
		
		foreach( $route[ 1 ] as $id => $val )
			if( !isset( $ret[ $id ] ) )
				$ret[ $id ] = $val;

		$controller -> run( $ret );
		return true;
	}
	
	function parseRoute( $route, $options ) {
		$ret = array();
		$route = explode( '/', $route );
		foreach( $route as $id => $val )
			if( $val[ 0 ] == '%' )
				$ret[] = array( substr( $val, 1, -1 ), true );
			else
				$ret[] = array( $val, false );
				
		return array( $ret, $options );
	}

	function addRoute( $route, $options = array() ) {
		$this -> routes[] = $this -> parseRoute( $route, $options );
	}
	
	function root( $controller, $action ) {
		$this -> root = array( 'controller' => $controller, 'action' => $action );
	}
	
	function resource( $name ) {
/*		$this -> routes[] = $this -> parseRoute( "$name/", array( 'action' => 'index' ) );
		$this -> routes[] = $this -> parseRoute( "$name/%id%", array( 'action' => 'view' ) );
		$this -> routes[] = $this -> parseRoute( "$name/%id%/edit", array( 'action' => 'edit' ) );*/
		
		$this -> routes[] = $this -> parseRoute( "$name/(%action%/(%id%))", array( 'controller' => $name, 'action' => 'index' ) );
	}
}

$router = new Router;

?>
