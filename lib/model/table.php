<?php

class ModelTable {
	var $cache = array();
	var $newRow = NULL;
	
	function __construct( $array = array() ) {
		if( !empty( $array ) ) 
			$this -> newRow = new ModelRow( $array );
	}
	
	function get( $row, $index ) {
		return $this -> cache[ $row ] -> $index;
	}
	
	function set( $row, $index, $value ) {
		$this -> cache[ $row ] -> $index = $value;
		
		return $this -> cache[ $row ];
	}
}

?>
