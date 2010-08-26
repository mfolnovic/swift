<?php

class TestSuite extends Base {
	var $results = array();
	var $currentTest = '';
	var $currentClass = '';
	var $vars = array();
	var $counts = array( 0 => 0, 1 => 0 );

	function __destruct() {
		$this -> printResults();
	}

	function __set( $index, $value ) {
		$this -> vars[ $index ] = $value;
	}

	function __get( $index ) {
		return $this -> vars[ $index ];
	}

	function load( $d ) {
		$files = Dir::files( $d );

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
			$case -> $method();
		}
	}

	function addResult( $result, $message ) {
		$this -> counts[ $result ] ++;
		$this -> results[] = array( $result, $this -> currentClass, $this -> currentTest, $this -> parseMessage( $message ) );
	}

	function printResults() {
		if( empty( $this -> results ) ) return;

		$last = ''; $i = 0;
		$s = ""; $r = '';

		foreach( $this -> results as $value )  {
			$s .= $value[ 0 ] ? '.' : 'F';
			if( $value[ 0 ] === false )
				$r .= ( ++ $i ) . ') ' . $value[ 2 ] . '(' . $value[ 1 ] . ')' . ( !empty( $value[ 3 ] ) ? ': ' . $value[ 3 ] : '' ) . PHP_EOL;
		}

		echo $s . PHP_EOL . PHP_EOL;
		if( $this -> counts[ 0 ] > 0 )
			echo $this -> colorize( "There were {$this -> counts[ 0 ]} failures:", "[37;41m" ) . PHP_EOL;
		else
			echo $this -> colorize( "Everything OK (" . count( $this -> results ) ." asserts)!", "[0;42m" ) . PHP_EOL;

		echo $r;
	}

	function colorize( $text, $color ) {
		return chr( 27 ) . $color . $text . chr( 27 ) . "[0m";
	}
	
	function parseMessage( $message ) {
		return preg_replace_callback( '!\{\{(\w+)\}\}!', function( $match ) { $index = $match[ 1 ]; return TestSuite::getInstance() -> $index; }, $message );
	}
}

?>
