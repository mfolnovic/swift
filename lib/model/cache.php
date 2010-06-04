<?php

class ModelCache {
	var $cache = array();
	
	function __get( $index ) {
		return $this -> cache[ $index ];
	}
	
	function __set( $index, $value ) {
		$this -> cache[ $index ] = $value;
	}
}

?>
