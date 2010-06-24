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

?>
