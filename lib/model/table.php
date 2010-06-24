<?php

class ModelTable {
	var $name = NULL;
	var $rows = array();
	var $columns;
	
	function __construct( $name ) {
		$this -> name = $name;
		$this -> getColumns();
	}
	
	function setRow( $row, $value ) {
		$this -> rows[ $row ] = $value;
	}
	
	function get( $row, $index ) {
		return $this -> rows[ $row ] -> $index;
	}
	
	function set( $row, $index, $value ) {
		$this -> rows[ $row ] -> $index = $value;
		
		return $this -> rows[ $row ];
	}
		
	function getColumns() {
		global $db;
		$r = $db -> query( "SHOW COLUMNS FROM " . ( $this -> name ) );
		
		while( $row = $db -> fetchRow( $r ) )
			$this -> columns[ $row[ 'Field' ] ] = $row;
	}
}

?>
