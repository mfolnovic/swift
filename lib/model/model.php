<?php

class Model {
	var $tables = array();
}

class ModelRow {
	var $row;
	
	function __construct( $row ) {
		$this -> row = $row;
	}
	
	function __get( $id ) {
		return $this -> row[ $id ];
	}
	
	function __set( $id, $value ) {	
		$this -> row[ $id ] = $value;
		
		return $this;
	}
	
	function __isset( $id ) {
		return isset( $this -> row[ $id ] );
	}
}

class ModelBase {
	var $name;
	var $validations = array();
	var $update = array();
	var $relation;
	var $currentDataSet = NULL;
	var $newRecord = false;
			
	function __construct( $data = array() ) {
//		global $model;
		$this -> name = isset( $this -> tableName ) ? $this -> tableName : strtolower( get_class( $this ) );

/*		if( !isset( $model -> tables[ $this -> name ] ) )
			$model -> tables[ $this -> name ] = new ModelTable( $this -> name );
*/
		if( !empty( $data ) ) {
			$this -> currentDataSet = new ModelRow( $data );
			$this -> newRecord = true;
		}
		
		$this -> relation = array( 'where' => array(), 'order' => '', 'select' => '*', 'limit' => array( 0 => -1 ), 'group' => '', 'having' => '' );
		$this -> init();
		
		return $this;
	}

	function __get( $name ) {
		$this -> currentDataSet = $this -> doQuery();
		return isset( $this -> currentDataSet -> $name ) ? $this -> currentDataSet -> $name : NULL; 
	}

	function all() {
		return $this -> doQuery( false );
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
	
	function doQuery( $one_result = true ) {
		global $db, $log;
		
		$this -> newRecord = false;
		if( !empty( $this -> currentDataSet ) ) return $this -> currentDataSet;

		$this -> currentDataSet = array();
		$res = $db -> query( $this -> constructQuery() );
		for( $i = 0; $row = $res -> fetch_assoc(); ++ $i ) {
//			$tmp = &$model -> tables[ $this -> name ] -> rows[ $row -> ID ];
			$this -> currentDataSet[] = new ModelRow( $row );
		}
		
		if( $i == 1 && $one_result ) $this -> currentDataSet = current( $this -> currentDataSet );
		
		$res -> free_result();
		return $this -> currentDataSet;
		//return $this -> currentDataSet = new ModelTableResult( $db -> query( $this -> constructQuery() ) );
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

	function invalid() {
		foreach( $this -> validations as $val ) {
			$field = $val[ 0 ];
			if( preg_match( $val[ 1 ], $this -> $field ) )
				return true;
		}
		
		return false;
	}

	function validates_format_of( $field, $regex, $message = '', $on = '' ) {
		$this -> validations[] = array( $field, $regex, $message, $on );
		
		return $this;
	}

	function __set( $name, $value ) {
		global $db, $model;
	
		$this -> update[ $name ] = $db -> safe( $value );
		
		return $this;
	}
	
	function values( $array ) {
		$this -> update = array_merge( $array, $this -> update );
	
		return $this;
	}
	
	function save() {
		if( $this -> newRecord ) $this -> createRow();
		else $this -> saveRow();
		
		return $this;
	}
	
	function delete() {
		$this -> query( "DELETE FROM " . ( $this -> name ) . ( $this -> generateWhere() ) );
	
		return $this;
	}
	
	function createRow() {
		global $db;
		
		$columns = '`' . implode( '`,`', array_keys( $this -> currentDataSet -> row ) ) . '`';
		$values = '';
		
		foreach( $this -> currentDataSet -> row as $id => $val )
			$values .= ( isset( $values[ 0 ] ) ? ',' : '' ) . ( $db -> safe( $val ) );
			
		$this -> query( "INSERT INTO " . ( $this -> name ) . " ( " . $columns . " ) VALUES ( " . $values . " )" );
	}
	
	function saveRow() {
		global $db;
		
		$q = "UPDATE " . ( $this -> name ) . " SET "; 
		$first = true;
		
		foreach( $this -> update as $id => $val ) {
			if( $id == "id" ) continue; // TEMP
			if( !$first ) { $q .= ", "; }
			else $first = false;
			
			$q .= '`' . $id . '` = ' . ( $db -> safe( $val ) );
		}
		
		$q .= ( $this -> generateWhere() ) . ( $this -> generateOrderBy() ) . ( $this -> generateLimit() );
		$db -> query( $q );
		
		return $this;
	}
	
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
		
		return $this;
	}
	
	/* range */
	protected function value( $o ) {
		global $db;
		
		if( !is_array( $o ) ) return " = " . $db -> safe( $o );
		else if( isset( $o[ 1 ] ) ) return " IN ( " . implode( ',' ) . " )";
		else return " = " . $o[ 0 ];
	}
};

$model = new Model;

?>

