<?php

class ModelRelation extends ModelCallbacks {
	var $relation;

	function where( $conditions ) {
		$this -> relation[ 'where' ] = $conditions;
	
		return $this;
	}
	
	
	function order( $by, $dir ) {
		$this -> relation[ 'order' ] = array( $by, $dir == 'asc' ) ;
		
		return $this;
	}
	
	function select( $fields ) {
		$this -> relation[ 'select' ] = $fields;
		
		return $this;
	}
	
	function limit( $by ) {
		$this -> relation[ 'limit' ][ 0 ] = $by;
		
		return $this;
	}
	
	function offset( $by ) {
		$this -> relation[ 'limit' ][ 1 ] = $by;
		
		return $this;
	}
	
	function group( $by ) {
		$this -> relation[ 'group' ] = ' GROUP BY ' . $by;
		
		return $this;
	}
	
	function having( $what ) {
		$this -> relation[ 'having' ] = ' HAVING ' . $what;
	}
}

?>
