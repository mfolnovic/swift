<?php

class ModelRelation extends ModelCallbacks {
	var $relation;
	
	function __call( $name, $arguments ) {
		global $db;
		
		if( substr( $name, 0, 7 ) == 'find_by' ) {
			$name = substr( $name, 8 );
			$this -> where( array( $name => $arguments[ 0 ] ) );
		}
		
		return $this;
	}

	function where( $conditions ) {
		$this -> newRecord = false;
		$this -> relation[ 'where' ] = $conditions;
	
		return $this;
	}

	function order( $by, $dir ) {
		$this -> newRecord = false;
		$this -> relation[ 'order' ] = array( $by, $dir == 'asc' ) ;
		
		return $this;
	}
	
	function select( $fields ) {
		$this -> newRecord = false;
		$this -> relation[ 'select' ] = $fields;
		
		return $this;
	}
	
	function limit( $by ) {
		$this -> newRecord = false;
		$this -> relation[ 'limit' ][ 0 ] = $by;
		
		return $this;
	}
	
	function offset( $by ) {
		$this -> newRecord = false;
		$this -> relation[ 'limit' ][ 1 ] = $by;
		
		return $this;
	}
	
	function group( $by ) {
		$this -> newRecord = false;
		$this -> relation[ 'group' ] = ' GROUP BY ' . $by;
		
		return $this;
	}
	
	function having( $what ) {
		$this -> newRecord = false;
		$this -> relation[ 'having' ] = ' HAVING ' . $what;
	}
	
	/* range */
	protected function value( $o ) {
		global $db;
		
		if( !is_array( $o ) ) return " = " . $db -> safe( $o );
		else if( isset( $o[ 1 ] ) ) return " IN ( " . implode( ',' ) . " )";
		else return " = " . $o[ 0 ];
	}
}

?>
