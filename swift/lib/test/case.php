<?php

class Test_Case {
	function setup() {}

	function assert( $result, $message = '' ) {
		Test_Suite::instance() -> addResult( $result, $message );
	}
	
	function assertEqual( $actual, $expected, $message = '' ) {
		$instance = Test_Suite::instance();

		$instance -> actual = $actual;
		$instance -> expected = $expected;

		if( empty( $message ) ) $message = "Got $actual, expected $expected!";

		$instance -> addResult( $actual == $expected, $message );
	}

	function assertEmpty( $result, $message ) {
		Test_Suite::instance() -> addResult( empty( $result ), $message );
	}
}

?>
