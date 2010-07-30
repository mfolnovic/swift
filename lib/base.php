<?php

class Base {
	var $before_filters = array();
	var $after_filters  = array();
	
	function __construct() {
		foreach( $this -> before_filters as $function )
			call_user_func( array( $this, $function ) );
	}
	
	function __destruct() {
		foreach( $this -> after_filters as $function )
			call_user_func( array( $this, $function ) );
	}
	
	function before_filter( $function ) {
		$this -> before_filters[] = $function;
	}
	
	function after_filter( $function ) {
		$this -> after_filters[] = $function;
	}
}

?>
