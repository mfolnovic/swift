<?php

class TestSuite extends Base {
	var $results = array();
	var $currentTest = '';
	var $currentClass = '';
	var $vars = array();
	static $instance = NULL;
	
	function __destruct() {
		$this -> printResults();
	}

	static function getInstance() {
		if( self::$instance == NULL ) self::$instance = new TestSuite;
		return self::$instance;
	}
	
	function __set( $index, $value ) {
		$this -> vars[ $index ] = $value;
	}
	
	function __get( $index ) {
		return $this -> vars[ $index ];
	}
	
	function load( $d ) {
		global $dir;
		$files = $dir -> files( $d );

		foreach( $files as $file ) {
			if( is_dir( $d . $file ) )
				$this -> load( $d . $file . '/' );
			else {
				include $d . $file;
				$this -> run( $file );
			}
		}
	}
	
	function run( $file ) {
		$obj = substr( $file, 0, -4 ) . 'Test';
		$this -> currentClass = $obj;
		$case = new $obj;
		$methods = get_class_methods( $case );
		
		foreach( $methods as $method ) {
			if( substr( $method, 0, 4 ) != "test" ) continue;
			$this -> currentTest = $method;
			@$case -> $method();
		}
	}

	function addResult( $result, $message ) {
		$this -> results[] = array( $result, $this -> currentClass, $this -> currentTest, $this -> parseMessage( $message ) );
	}

	function printResults() {
		if( empty( $this -> results ) ) return;
	
		$last = ''; $i = 0;
		$s = ""; $r = '';
		foreach( $this -> results as $value )  {
			$s .= $value[ 0 ] ? '.' : 'F';
			if( $value[ 0 ] === false )
				$r .= ( ++ $i ) . ') ' . $this -> colorize( "FAILURE", "[0;31m" ) . PHP_EOL . $value[ 2 ] . '(' . $value[ 1 ] . ')' . ( !empty( $value[ 3 ] ) ? ': ' . $value[ 3 ] : '' ) . PHP_EOL;
		}
		
		echo $s . PHP_EOL . PHP_EOL . $r;
	}

	function colorize( $test, $color ) {
		return chr( 27 ) . $color . $test . chr( 27 ) . "[0m";;
	}
	
	function parseMessage( $message ) {
		return preg_replace_callback( '!\{\{(\w+)\}\}!', function( $match ) { $index = $match[ 1 ]; return TestSuite::getInstance() -> $index; }, $message );
	}
}

?>
