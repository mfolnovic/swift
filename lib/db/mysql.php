<?php

// Mysql driver
class Mysql extends Base {
	var $conn = NULL;
	var $last_query;
	var $numrows;
	var $options;

	function safe( $str ) {
		if( !$this -> conn ) $this -> connect();
		if( get_magic_quotes_gpc() ) $str = stripslashes( $string );
		if( !is_numeric( $str ) ) $str = "'" . mysqli_real_escape_string( $this -> conn, $str ) . "'";
		
		return $str;
	}
	
	function connect() {
		$this -> conn = new mysqli( $this -> options[ 'host' ], $this -> options[ 'username' ], $this -> options[ 'password' ], $this -> options[ 'database' ] );
	}
	
	function __destruct() {
		if( !$this -> conn ) return;
		$this -> conn -> close();
	}

	function query( $q ) {
		if( !$this -> conn ) $this -> connect();
		
		return $this -> conn -> query( $q );
	}

	/**
	 * Constructs query based on current relation
	*/
	function constructQuery( &$model ) {
		$q = "SELECT " . ( $model -> relation[ 'select' ] ) . " FROM " . ( $model -> tableName ) . ( $this -> generateWhere( $model ) ) . ( $model -> relation[ "group" ] ) . ( $model -> relation[ 'having' ] ) . ( $this -> generateOrderBy( $model ) ) . ( $this -> generateLimit( $model ) );
		
		return $q . ';';
	}
	
	/**
	 * Generates order by part of query based on current relation
	*/
	function generateOrderBy( &$model ) {
		$o = $model -> relation[ 'order' ];
		if( $o != '' ) $o = ' ORDER BY ' . $o[ 0 ] . ' ' . ( $o[ 1 ] ? 'asc' : 'desc' );
		
		return $o;
	}
	
	/**
	 * Generates limit part of query based on current relation
	*/
	function generateLimit( &$model ) {
		if( $model -> relation[ 'limit' ][ 0 ] != -1 )
			return ' LIMIT ' . implode( ',', $model -> relation[ 'limit' ] );
		else
			return '';
	}

	/**
	 * Generates where part of query based on current relation
	*/
	function generateWhere( &$model ) {
		$first = true; $ret = '';
		foreach( $model -> relation[ 'where' ] as $id => $val ) {
			if( $ret == '' ) $ret .= ' WHERE ';
			else $ret .= " AND ";
			
			$ret .= '`' . $id . '`' . ( $this -> value( $val ) );
		}
		
		return $ret;
	}
	
	/**
	 * Does query for current relation, and returns array of rows
	 * @param bool $one_result Tels if query gets only one row, can it just return it, instead of returning array
	*/
	function doQuery( &$model, $one_result = true ) {
		$model -> newRecord = false;
		$model -> currentDataSet = array();
		$res = $this -> query( $this -> constructQuery( $model ) );
		for( $i = 0; $row = $res -> fetch_assoc(); ++ $i ) {
//			$tmp = &$model -> tables[ $this -> name ] -> rows[ $row -> ID ];
			$model -> currentDataSet[] = new ModelRow( $row );
		}
		
		$res -> free_result();
		if( $i == 1 && $one_result ) $model -> currentDataSet = $model -> currentDataSet[ 0 ];

		return $model -> currentDataSet;
		//return $this -> currentDataSet = new ModelTableResult( $db -> query( $this -> constructQuery() ) );
	}

	function save( &$model ) {
		if( $model -> newRecord ) {
			if( !$model -> valid( $model -> currentDataSet -> row ) ) return $model;	
			
			$columns = '`' . implode( '`,`', array_keys( $model -> currentDataSet -> row ) ) . '`';
			$values = '';
		
			foreach( $model -> currentDataSet -> row as $id => $val )
				$values .= ( isset( $values[ 0 ] ) ? ',' : '' ) . ( $this -> safe( $val ) );
			
			$this -> query( "INSERT INTO " . ( $model -> tableName ) . " ( " . $columns . " ) VALUES ( " . $values . " )" );
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
	
	function dropAndCreateTable() {
		$this -> link -> query( "DROP TABLE IF EXISTS " . $this -> tableName );
		
		$q = "CREATE TABLE " . $this -> tableName . " (";
		$first = true;
		foreach( $this -> schema as $field => $desc ) {
			if( !$first ) $q .= ',';
			
			$q .= '`' . $field . '` ' . $desc[ 'type' ];
			if( isset( $desc[ 'size' ] ) ) $q .= '(' . $desc[ 'size' ] . ')';
			if( isset( $desc[ 'default' ] ) ) $q .= ' DEFAULT ' . $desc[ 'default' ];
			if( isset( $desc[ 'auto_increment' ] ) ) $q .= ' AUTO_INCREMENT';
			$first = false;
		}
		
		foreach( $this -> schema_keys as $field => $type ) {
			$q .= ',';
			if( $type == 'primary' ) $q .= "PRIMARY KEY (`$field`)";
			// add support for other types of keys
			$first = false;
		}
		$q .= ') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;';
		
		$this -> link -> query( $q );
	}
	
	/* range */
	protected function value( $o ) {
		if( !is_array( $o ) ) return " = " . $this -> safe( $o );
		else if( isset( $o[ 1 ] ) ) return " IN ( " . implode( ',' ) . " )";
		else return " = " . $o[ 0 ];
	}
}

?>
