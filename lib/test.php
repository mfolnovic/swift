<?php

class Test extends Base {
	var $results = array();
	var $currentTest = '';
	var $currentClass = '';

	function __destruct() {
		$this -> printResults();
	}

	function load( $d ) {
		global $dir;
		$files = $dir -> files( $d );

		foreach( $files as $file ) {
			include $d . $file;
			$this -> run( $file );
		}
	}
	function run( $file ) {
		$obj = substr( $file, 0, -4 ) . 'Test';
		$this -> currentClass = $obj;
		$case = new $obj;
		$methods = array_diff( get_class_methods( $case ), get_class_methods( 'TestCase' ) );
		
		foreach( $methods as $method ) {
			$this -> currentTest = $method;
			$case -> $method();
		}
	}

	function addResult( $result ) {
		$this -> results[] = $result;
	}

	function printResults() {
		$last = ''; $i = 0;
		$s = ""; $r = '';
		foreach( $this -> results as $value )  {
//			if( $last != $this -> currentTest ) { $i = 0; $r .= "Test " . $this -> currentTest . PHP_EOL; $last = $this -> 	currentTest; }

			$s .= $value ? '.' : 'F';
			if( $value == 0 )
				$r .= ( ++ $i ) . ') ' . $this -> colorize( "FAILURE", "[0;31m" ) . PHP_EOL . $this -> currentTest . '(' . $this -> currentClass . ')' . PHP_EOL;
		}
		
		echo $s . PHP_EOL . PHP_EOL . $r;
	}

	function colorize( $test, $color ) {
		return chr( 27 ) . $color . $test . chr( 27 ) . "[0m";;
	}
}

$test = new Test;

?>
