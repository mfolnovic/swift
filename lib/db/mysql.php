<?php

// Mysql driver
class Mysql extends Base {
	var $conn = NULL;
	var $last_query;
	var $numrows;
	var $options;

	function safe( $str ) {
		if( $str[ 0 ] == '`' ) return $str;
		if( !$this -> conn ) $this -> connect();
		if( get_magic_quotes_gpc() ) $str = stripslashes( $string );
		if( !is_numeric( $str ) ) $str = "'" . mysqli_real_escape_string( $this -> conn, $str ) . "'";
		
		return $str;
	}
	
	function connect() {
		$this -> conn = new mysqli( $this -> options[ 'host' ], $this -> options[ 'username' ], $this -> options[ 'password' ], $this -> options[ 'database' ] );
	}
	
	function __destruct() {
		if( $this -> conn ) 
			$this -> conn -> close();
	}

	function query( $q ) {
		if( !$this -> conn ) $this -> connect();

		global $log; $log -> write( $q );
		return $this -> conn -> query( $q );
	}

	/**
	 * Generates where part of query based on current relation
	*/
	function generateWhere( &$model ) {
		$ret = '';
		foreach( $model -> relation[ 'where' ] as $id => $val )
			$ret .= ( $ret == '' ? ' WHERE ' : ' AND ' ) . '`' . $id . '`' . $this -> value( $val );
		return $ret;
	}
	
	/**
		Generates parts of query: limit, groupby, order
		TODO
	*/
	function generateExtra( &$model ) {
		return '';
	}
	
	/**
	 * Does query for current relation, and returns array of rows
	*/
	function select( &$base ) {
		global $model;
		$base -> resultSet = array();
		$table = &$model -> tables[ $base -> tableName ];
		
		$relation = &$base -> relation;
		if( empty( $relation[ 'select' ] ) ) $select = '*';
		else $select = 'id,' . implode( ',', $relation[ 'select' ] );

		$res = $this -> query( "SELECT " . $select . " FROM " . $base -> tableName . $this -> generateWhere( $base ) . $this -> generateExtra( $base ) . ';' );

		for( $i = 0; $i < $res -> num_rows; ++ $i ) {
			$row = $res -> fetch_assoc();
			$table[ $row[ 'id' ] ] = new ModelRow( $row );
			
			$base -> resultSet[ $row[ 'id' ] ] = &$table[ $row[ 'id' ] ];
		}
	}

	protected function value( $o ) {
		if( !is_array( $o ) ) return " = " . $this -> safe( $o );
		else return " IN ( " . implode( ',', $o ) . " )"; // sql injection!!
//		else return " = " . $this -> safe( $o[ 0 ] );
	}
}

?>
