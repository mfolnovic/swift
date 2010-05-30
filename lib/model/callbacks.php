<?php

class ModelCallbacks extends ModelValidations {
	var $update = array();

	function __set( $name, $value ) {
		global $db;
	
		$this -> update[ $name ] = $db -> safe( $value );
		
		return $this;
	}
	
	function save() {
		global $db;
		
		$q = "UPDATE " . ( $this -> name ) . " SET "; 
		$first = true;
		
		foreach( $this -> update as $id => $val ) {
			if( !$first ) { $q .= " AND "; }
			else $first = false;
			
			$q .= '`' . $id . '` = ' . ( $db -> safe( $value ) );
		}
		
		$q .= ( $this -> generateWhere() ) . ( $this -> generateOrderBy() ) . ( $this -> generateLimit() );
		echo $q;
		
//		$db -> query( $q );
		
		return $this;
	}
}

?>
