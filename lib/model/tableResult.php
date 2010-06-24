<?php

class ModelRow {
	var $row;
	
	function __construct( $row ) {
		$this -> row = $row;
	}
	
	function __get( $id ) {
		return $this -> row[ $id ];
	}
	
	function __set( $id, $value ) {	
		$this -> row[ $id ] = $value;
		
		return $this;
	}
}

class ModelTableResult implements Iterator {
	var $rows = array();
	var $size = 0;
	
	function __construct( $res ) {
		while( $row = $res -> fetch_assoc() ) {
//			$tmp = &$model -> tables[ $this -> name ] -> rows[ $row -> ID ];
			$this -> rows[] = new ModelRow( $row );
		}
		
		$res -> free_result();
	}
		
	function __get( $index ) {
		$curr = $this -> current();
		return $curr -> $index;
	}
	
/*	function __set( $index, $value ) {
		$curr = $this -> current();
		$curr -> $index = $value;
		
		return $this;
	}*/
	
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
		return current( $this -> rows ) !== false;
	}  
}

?>
