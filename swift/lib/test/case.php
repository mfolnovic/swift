<?php

class Test_Case {
	function assert( $result, $message = '' ) {
		Test_Suite::instance() -> addResult( $result, $message );
	}
	
	function assertEqual( $actual, $expected, $message = '' ) {
		$instance = Test_Suite::instance();

		$instance -> actual = $actual;
		$instance -> expected = $expected;
		
		$instance -> addResult( $actual == $expected, $message );
	}
}
	
?>
