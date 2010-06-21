<?php

class Benchmark extends Base {
	var $times = array();
	
	function start( $name ) {
//		if( !FRAMEWORK_BENCHMARK ) return;
		$this -> times[ $name ] = microtime();
	}
	
	function end( $name ) {
		global $log;
//		if( !FRAMEWORK_BENCHMARK ) return;
		$log -> log( "[Benchmark] $name: " . round( ( microtime() - $this -> times[ $name ] ), 4 ) . " seconds!" );
	}
}

$benchmark = new Benchmark;

?>
