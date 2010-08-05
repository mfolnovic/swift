<?php

class TestCase {
	function assert( $result, $message = '' ) {
		TestSuite::getInstance() -> addResult( $result, $message );
	}
	
	function assertEqual( $actual, $expected, $message = '' ) {
		$instance = TestSuite::getInstance();

		$instance -> actual = $actual;
		$instance -> expected = $expected;
		
		$instance -> addResult( $actual == $expected, $message );
	}
}
	
?>
