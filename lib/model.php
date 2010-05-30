<?php

class Model extends Base {
	var $name;
	var $relation;
	
	function __construct() {
		$this -> name = strtolower( get_called_class() );
		$this -> relation = array( 'where' => array(), 'order' => '', 'select' => '*', 'limit' => array(), 'group' => '', 'having' => '' );
		
		$this -> init();
	}
	
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
	
	function __get( $name ) {
		global $db;
	
		$r = $this -> doQuery();
		
		if( $db -> numrows == 0 ) die( "No rows" );
		
		return $r[ 0 ][ $name ];
	}
	
	function __set( $name, $value ) {
	}
	
	private function doQuery() {
		global $db;
		
		$q = $this  -> constructQuery();
		$r = $db -> allRows( $db -> query( $q ) );
		
		return $r;
	}
	
	private function constructQuery() {
		$o = $this -> relation[ 'order' ];
		$o = $o == '' ? '' : ' ORDER BY ' . $o[ 0 ] . ' ' . ( $o[ 1 ] ? 'asc' : 'desc' );
		
		$l = $this -> relation[ 'limit' ];
		$l = empty( $l ) ? '' : ' LIMIT ' . implode( ',', $l );
		
		$q = "SELECT " . ( $this -> relation[ 'select' ] ) . " FROM " . ( $this -> name ) . 's' . ( $this -> generateWhere() ) . ( $this -> relation[ "group" ] ) . ( $this -> relation[ 'having' ] ) . $o . $l;
//		echo $q;exit;
		return $q . ';';
	}
	
	private function generateWhere() {
		global $db;
	
		$first = true; $ret = '';
		foreach( $this -> relation[ 'where' ] as $id => $val ) {
			$ret .= ' ';
			if( !$first ) { $ret .= "AND "; $first = false; }
			else $ret .= ' WHERE';
			
			$ret .= '`' . $id . '` = ' . ( $db -> safe( $val ) );
		}
		
		return $ret;
	}
}

?>
