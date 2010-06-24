<?php

class ModelTableResult implements Iterator {
	var $rows = array();
	var $size = 0;
		
	function __get( $index ) {
		$curr = $this -> current();
		return $curr -> $index;
	}
	
	function __set( $index, $value ) {
		$curr = $this -> current();
		$curr -> $index = $value;
		
		return $this;
	}
	
	function rewind() {
		reset( $this -> rows );
	}
	
	function current() {
		return current( $this -> rows );
	}
	
	function key() {
		return key( $this -> rows );
	}
	
	function next() {
		return next( $this -> rows );
	}
	
	function valid() {
		return $this -> current() !== false;
	}  
	
	function push( $row ) {
		$this -> rows[ $row -> ID ] = $row;
		++ $this -> size;
	}
}

?>
