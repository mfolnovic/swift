<?php

class ModelQuery extends Base {
	function __get( $name ) {
		global $db, $controller;

		$this -> currentDataSet = $this -> doQuery();

		$r = &$this -> currentDataSet;
		$c = count( $r ); // cache this
		if( $c == 0 ) $controller -> render404();
		else if( $c == 1 ) $r = current( $r );
		
		return isset( $r -> $name ) ? $r -> $name : NULL;
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
	
	function fetchAll( $r ) {
		global $model;

		$ret = new ModelTableResult;
		
		while( $row = mysql_fetch_assoc( $r ) ) {
			$row = new ModelRow( $row );
			$tmp = &$model -> tables[ $this -> name ] -> rows[ $row -> ID ];
			$tmp = $row; 
			$ret -> push( $tmp );
		}
		
		mysql_free_result( $r );
			
		return $ret;
	}
	
	function doQuery() {
		global $db, $log;
		
		$this -> newRecord = false;
		if( !empty( $this -> currentDataSet ) ) return $this -> currentDataSet;
		
		$q = $this  -> constructQuery();
		$r = $this -> fetchAll( $db -> query( $q ) );

//		if( count( $r ) == 1 && abs( $this -> relation[ 'limit' ][ 0 ] - 1 ) == 1 ) $r = current( $r ); // is this always first ? or should I reset pointer ? abs( ... ) so it it's true for 0 & 1, but not for -1 and >1
		
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
		if( $this -> relation[ 'limit' ][ 0 ] != -1 )
			return ' LIMIT ' . implode( ',', $this -> relation[ 'limit' ] );
		else
			return '';
	}
	
	function generateWhere() {
		global $db;
	
		$first = true; $ret = '';
		foreach( $this -> relation[ 'where' ] as $id => $val ) {
			if( $ret == '' ) $ret .= ' WHERE ';
			else $ret .= " AND ";
			
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
