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

		global $log;
		$log -> write( $q );
		return $this -> conn -> query( $q );
	}

	/**
	 * Generates where part of query based on current relation
	*/
	function generateWhere( &$model ) {
		$ret = '';
		foreach( $model -> relation[ 'where' ] as $id => $val ) {
			if( $ret == '' ) $ret .= ' WHERE ';
			else $ret .= " AND ";
			
			$ret .= '`' . $id . '`' . ( $this -> value( $val ) );
		}
		
		return $ret;
	}
	
	/**
		Generates parts of query: limit, groupby, order
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

	function save( &$model ) {
		if( $model -> newRecord ) {
			if( !$model -> valid( $model -> currentDataSet -> row ) ) return $model;	
			
			$columns = '`' . implode( '`,`', array_keys( $model -> currentDataSet -> row ) ) . '`';
			$values = '';
		
			foreach( $model -> currentDataSet -> row as $id => $val )
				$values .= ( isset( $values[ 0 ] ) ? ',' : '' ) . ( $this -> safe( $val ) );
			
			$this -> query( "INSERT INTO " . ( $model -> tableName ) . " ( " . $columns . " ) VALUES ( " . $values . " )" );
			$model -> id = $this -> conn -> insert_id;
		}	else {
			if( !$model -> valid( $model -> update ) ) return $model;			

			$q = "UPDATE " . ( $model -> tableName ) . " SET "; 
			$first = true;
			
			foreach( $model -> update as $id => $val ) {
				if( $id == "id" ) continue; // TEMP
				if( !$first ) { $q .= ", "; }
				else $first = false;
			
				$q .= '`' . $id . '` = ' . ( $this -> safe( $val ) );
			}
		
			$q .= ( $this -> generateWhere( $model ) ) . ( $this -> generateOrderBy( $model ) ) . ( $this -> generateLimit( $model ) );
			$this -> query( $q );
		}		
		
		return $model;
	}
	
	function delete() {
		$this -> link -> query( "DELETE FROM " . ( $this -> tableName ) . ( $this -> generateWhere() ) );
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
	
	/* range */
	protected function value( $o ) {
		if( !is_array( $o ) ) return " = " . $this -> safe( $o );
		else return " IN ( " . implode( ',', $o ) . " )"; // sql injection!!
//		else return " = " . $this -> safe( $o[ 0 ] );
	}
}

?>
