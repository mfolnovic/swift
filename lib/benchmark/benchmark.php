<?php

class Benchmark extends Base {
	static $times = array();
	
	static function start( $name ) {
		self::$times[ $name ] = microtime( true );
	}
	
	static function end( $name ) {
		Log::getInstance() -> write( "[Benchmark] $name: " . round( ( microtime( true ) - self::$times[ $name ] ), 4 ) . " seconds!" );
	}
}

?>
