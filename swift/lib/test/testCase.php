<?php

class TestCase {
	function assert( $result, $message = '' ) {
		TestSuite::instance() -> addResult( $result, $message );
	}
	
	function assertEqual( $actual, $expected, $message = '' ) {
		$instance = TestSuite::instance();

		$instance -> actual = $actual;
		$instance -> expected = $expected;
		
		$instance -> addResult( $actual == $expected, $message );
	}
}
	
?>
