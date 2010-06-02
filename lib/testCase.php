<?php

class TestCase {
	var $test;

	function __construct() {}
	function assert( $a ) {
		global $test;

		$test -> addResult( $a );
	}
}
	
?>
