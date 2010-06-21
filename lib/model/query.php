<?php

class ModelQuery extends Base {
	function __get( $name ) {
		global $db, $controller;
	
		$r = $this -> doQuery();
		
		if( $db -> numrows == 0 ) $controller -> render404();
		
		return $r -> $name;
	}

	function all() {
		return $this -> doQuery();
	}
	
	function first() {
		$this -> limit( 1 );
	
		return $this -> doQuery();
	}
	
	function last() {
		$this -> limit( 1 );
		
		$o = $this -> relation[ 'order' ];
		$this -> order( $o[ 0 ], !$o[ 1 ] );
		
		return $this -> doQuery();
	}
	
	function find( $id ) {
		$this -> where( array( 'ID', $id ) );
		
		return $this -> doQuery();
	}
	
	function doQuery() {
		global $db;
		
		$q = $this  -> constructQuery();
		$r = $db -> allRows( $db -> query( $q ) );
		
		if( count( $r ) == 1 ) $r = $r[ 0 ];
		
		return $r;
	}
	
	function constructQuery() {
		$q = "SELECT " . ( $this -> relation[ 'select' ] ) . " FROM " . ( $this -> name ) . ( $this -> generateWhere() ) . ( $this -> relation[ "group" ] ) . ( $this -> relation[ 'having' ] ) . ( $this -> generateOrderBy() ) . ( $this -> generateLimit() );
		
		return $q . ';';
	}
	
	function generateOrderBy() {
		$o = $this -> relation[ 'order' ];
		if( $o != '' ) $o = ' ORDER BY ' . $o[ 0 ] . ' ' . ( $o[ 1 ] ? 'asc' : 'desc' );
		
		return $o;
	}
	
	function generateLimit() {
		$l = implode( ',', $this -> relation[ 'limit' ] );
		if( $l != '' ) $l = ' LIMIT ' . $l;
		
		return $l;
	}
	
	function generateWhere() {
		global $db;
	
		$first = true; $ret = '';
		foreach( $this -> relation[ 'where' ] as $id => $val ) {
			$ret .= ' ';
			if( !$first ) { $ret .= "AND "; $first = false; }
			else $ret .= ' WHERE';
			
			$ret .= '`' . $id . '`' . ( $this -> value( $val ) );
		}
		
		return $ret;
	}
	
	function query( $q ) {
		global $db;
		
		return $db -> query( $q );
	}
}

?>
