<?php

class TestCase {
	var $test;

	function __construct() {}
	function assert( $result, $message = '' ) {
		global $test;

		$test -> addResult( $result, $message );
	}
}
	
?>
