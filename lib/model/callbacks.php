<?php

class ModelCallbacks extends ModelValidations {
	var $update = array();

	function __set( $name, $value ) {
		global $db, $model;
	
		$this -> update[ $name ] = $db -> safe( $value );
		
		return $this;
	}
	
	function values( $array ) {
		$this -> update = array_merge( $array, $this -> update );
	
		return $this;
	}
	
	function save() {
		if( $this -> newRecord ) $this -> createRow();
		else $this -> saveRow();
		
		return $this;
	}
	
	function delete() {
		$this -> query( "DELETE FROM " . ( $this -> name ) . ( $this -> generateWhere() ) );
	
		return $this;
	}
	
	function createRow() {
		global $db;
		
		$columns = '`' . implode( '`,`', array_keys( $this -> currentDataSet -> row ) ) . '`';
		$values = '';
		
		foreach( $this -> currentDataSet -> row as $id => $val )
			$values .= ( isset( $values[ 0 ] ) ? ',' : '' ) . ( $db -> safe( $val ) );
			
		$this -> query( "INSERT INTO " . ( $this -> name ) . " ( " . $columns . " ) VALUES ( " . $values . " )" );
	}
	
	function saveRow() {
		global $db;
		
		$q = "UPDATE " . ( $this -> name ) . " SET "; 
		$first = true;
		
		foreach( $this -> update as $id => $val ) {
			if( $id == "id" ) continue; // TEMP
			if( !$first ) { $q .= ", "; }
			else $first = false;
			
			$q .= '`' . $id . '` = ' . ( $db -> safe( $val ) );
		}
		
		$q .= ( $this -> generateWhere() ) . ( $this -> generateOrderBy() ) . ( $this -> generateLimit() );
		$db -> query( $q );
		
		return $this;
	}
}

?>
