<?php

class Benchmark extends Base {
	var $times = array();
	
	function start( $name ) {
		$this -> times[ $name ] = microtime();
	}
	
	function end( $name ) {
		global $log;
		$log -> log( "[Benchmark] $name: " . round( ( microtime() - $this -> times[ $name ] ), 4 ) . " seconds!" );
	}
}

$benchmark = new Benchmark;

?>
