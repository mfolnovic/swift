<?php

class TestCase {
	function assert( $result, $message = '' ) {
		TestSuite::getInstance() -> addResult( $result, $message );
	}
	
	function assertEqual( $first, $second, $message = '' ) {
		$instance = TestSuite::getInstance();
		$instance -> first = $first;
		$instance -> second = $second;
		
		$instance -> addResult( $first == $second, $message );
	}
}
	
?>
