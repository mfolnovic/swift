<?php

// Mysql driver
class Mysql extends Base {
	var $conn = NULL;
	var $last_query;
	var $numrows;
	var $options;
	
	function __construct( $options ) {
		$this -> options = $options;
	}

	function safe( $str ) {
		if( $str[ 0 ] == '`' ) return $str;
		if( !$this -> conn ) $this -> connect();
		if( !is_numeric( $str ) ) $str = "'" . mysqli_real_escape_string( $this -> conn, $str ) . "'";
		
		return $str;
	}
	
	function connect() {
		$this -> conn = new mysqli( $this -> options[ 'host' ], $this -> options[ 'username' ], $this -> options[ 'password' ], $this -> options[ 'database' ] );
		$this -> conn -> set_charset( 'utf8' );
	}
	
	function __destruct() {
		if( $this -> conn ) 
			$this -> conn -> close();
	}

	function query( $q ) {
		if( !$this -> conn ) $this -> connect();

		Benchmark::start( "[SQL $q]" );
		$r = $this -> conn -> query( $q );
		Benchmark::end( "[SQL $q]" );
		return $r;
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
		if( $base -> relationChanged === false ) return;
		
		$base -> resultSet = array();
		$base -> relationChanged = false;
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
		
		$base -> handleAssociations();
	}
	
	function save( &$base ) {
		if( !empty( $base -> newRecord ) ) {
			global $model;
			// insert into
			$columns = '';
			$values = '';

			foreach( $base -> newRecord as $id => $val ) {
				$columns.= ( $values == '' ? '' : ',' ) . '`' . $id . '`';
				$values .= ( $values == '' ? '' : ',' ) . $this -> safe( $val );
			}
			
			$this -> query( "INSERT INTO {$base -> tableName} ($columns) VALUES ($values)" );
			$base -> newRecord -> id = $this -> conn -> insert_id;
			
			$table = &$model -> tables[ $base -> tableName ];
			$table[ $base -> newRecord -> id ] = $base -> newRecord;
			$base -> resultSet[ $base -> newRecord -> id ] = & $table[ $base -> newRecord -> id ];
			$base -> newRecord = false;
		} else {
			$set = '';
			foreach( $base -> update as $id => $val ) 
				$set .= ( $set == '' ? '' : ',' ) . "`$id`=" . $this -> safe( $val );

			$this -> query( "UPDATE {$base -> tableName} SET $set " . $this -> generateWhere( $base ) );
		}
	}
	
	function delete( &$base ) {
		$this -> query( "DELETE FROM {$base -> tableName}" . $this -> generateWhere( $base ) );
	}

	function dropAndCreateTable( &$model ) {
		$this -> query( "DROP TABLE IF EXISTS " . $model -> tableName );
		
		$q = "CREATE TABLE " . $model -> tableName . " (";
		$first = true;
		foreach( $model -> schema as $field => $desc ) {
			if( !$first ) $q .= ',';
			
			$q .= '`' . $field . '` ' . $desc[ 'type' ];
			if( isset( $desc[ 'size' ] ) ) $q .= '(' . $desc[ 'size' ] . ')';
			if( isset( $desc[ 'default' ] ) ) $q .= ' DEFAULT ' . $desc[ 'default' ];
			if( isset( $desc[ 'auto_increment' ] ) ) $q .= ' AUTO_INCREMENT';
			$first = false;
		}
		
		foreach( $model -> schema_keys as $field => $type ) {
			$q .= ',';
			if( $type == 'primary' ) $q .= "PRIMARY KEY (`$field`)";
			// add support for other types of keys
			$first = false;
		}
		$q .= ') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;';
		
		$this -> query( $q );
	}

	protected function value( $o ) {
		if( !is_array( $o ) ) return " = " . $this -> safe( $o );
		else return " IN ( " . implode( ',', $o ) . " )"; // sql injection!!
//		else return " = " . $this -> safe( $o[ 0 ] );
	}
}

?>
