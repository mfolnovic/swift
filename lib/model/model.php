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

/**
 * Base model, allows creating ActiveRecord like models
 * Example:
 * <code>
 * // in controller
 *
 * // creating new instance of model User
 * $user = $this -> model( 'user' );
 *
 * // finding a user by username
 * $user = $this -> model( 'user' ) -> find_by_username( 'username' );
 *
 * // creating new user from POST data
 * $user = $this -> model( 'user', $this -> data ) -> save();
 *
 * // updating record from POST data
 * $user = $this -> model( 'user' ) -> find_by_ID( $this -> data[ "id" ] ) -> values( $this -> data ) -> save();
 * 
 * // deleting record
 * $this -> model( 'user' ) -> find_by_ID( $this -> data[ "id"]  ) -> delete();
 * 
 * // using other relations
 * $user = $this -> model( 'user' ) -> where( "`username` like '%multi%'" ) -> order( 'ID desc' ) -> select( 'username, password' );
 * // This will create query (but not run, until you want to get any field/row): SELECT username, password FROM user WHERE `username` like '%multi%' ORDER BY `ID` desc;
 * 
 * // you can also access attributes
 * $user = $this -> model( 'user' ) -> first();
 * echo $user -> username;
 * </code>
 *
 */
  
class ModelBase {
	/**
	 * Table name
	 * @var string $tableName
	*/
	var $tableName;

	/**
	 * Array containing validations
	*/
	var $validations = array();
	/**
	 * Array containing all errors during validation
	*/
	var $errors = array();
	/**
	 * Array containing names of columns that were updated
	*/
	var $update = array();
	/**
	 * Contains current relation
	*/
	var $relation;
	/**
	 * Contains data from last query
	*/
	var $currentDataSet = NULL;
	var $newRecord = false;
	var $dropAndCreateTable = false; // used for automatic creating table
	var $schema = array();
	
	/**
	 * ModelBase constructor
	 * @param array $newRecord Contains array of data which will be inserted in db when save() is run on the model 
	*/
	
	function __construct( $newRecord = array() ) {
//		global $model;
		if( empty( $this -> tableName ) ) $this -> tableName = strtolower( get_class( $this ) );

/*		if( !isset( $model -> tables[ $this -> name ] ) )
			$model -> tables[ $this -> name ] = new ModelTable( $this -> name );
*/

		if( $this -> dropAndCreateTable ) $this -> dropAndCreateTable();
		if( !empty( $newRecord ) ) {
			$this -> currentDataSet = new ModelRow( $newRecord );
			$this -> newRecord = true;
		}
		
		$this -> relation = array( 'where' => array(), 'order' => '', 'select' => '*', 'limit' => array( 0 => -1 ), 'group' => '', 'having' => '' );
		$this -> init();
		
		return $this;
	}

	/**
	 * Magic method used for getting a attribute
	 * Example
	 * <pre>
	 * // in controller
	 * $username = $this -> model( 'user' ) -> first() -> username;
	 * </pre>
	 * @param string $name Name of attribute to get
	 */
	function __get( $name ) {
		if( empty( $this -> currentDataSet ) ) $this -> currentDataSet = $this -> doQuery();
		return isset( $this -> currentDataSet -> $name ) ? $this -> currentDataSet -> $name : NULL; 
	}
	
	/**
	 * Magic method used for setting a attribute to value
	 * Example
	 * <pre>
	 * // in controller
	 * $user = $this -> model( 'user' ) -> first(); // gets first user
	 * $user -> username = 'foo'; // sets username to 'foo'
	 * </pre>
	*/

	function __set( $name, $value ) {
		global $db, $model;

		if( empty( $this -> currentDataSet ) ) $this -> currentDataSet = $this -> doQuery(); // temporary
		$this -> currentDataSet -> $name = $value;

		$this -> update[ $name ] = $value;//& $this -> currentDataSet -> row[ $name ];
		
		return $this;
	}
	
	/**
	 * Magic method used to handle calls like _find_by_username
	 * @param string $name Name of called function
	 * @param array $arguments Array of arguments passed to function
	*/
	function __call( $name, $arguments ) {
		global $db;
		
		if( substr( $name, 0, 7 ) == 'find_by' ) {
			$name = substr( $name, 8 );
			$this -> where( array( $name => $arguments[ 0 ] ) );
		}
		
		return $this;
	}

	/**
	 * Used to get all records for current relation
	*/
	function all() {
		return $this -> doQuery( false );
	}
	
	/**
	 * Used to get first record for current relation
	*/
	function first() {
		$this -> limit( 1 );
	
		return $this -> doQuery();
	}
	
	/**
	 * Used to get last record for current relation
	*/
	function last() {
		$this -> limit( 1 );
		
		$o = $this -> relation[ 'order' ];
		$this -> order( $o[ 0 ], !$o[ 1 ] );
		
		return $this -> doQuery();
	}
	
	/**
	 * Same as find_by_id( 5 )
	*/
	function find( $id ) {
		$this -> where( array( 'ID', $id ) );
		
		return $this -> doQuery();
	}
	
	/**
	 * Does query for current relation, and returns array of rows
	 * @param bool $one_result Tels if query gets only one row, can it just return it, instead of returning array
	*/
	function doQuery( $one_result = true ) {
		global $db, $log;
		
		$this -> newRecord = false;
		$this -> currentDataSet = array();
		$res = $db -> query( $this -> constructQuery() );
		for( $i = 0; $row = $res -> fetch_assoc(); ++ $i ) {
//			$tmp = &$model -> tables[ $this -> name ] -> rows[ $row -> ID ];
			$this -> currentDataSet[] = new ModelRow( $row );
		}
		
		$res -> free_result();
		if( $i == 1 && $one_result ) $this -> currentDataSet = $this -> currentDataSet[ 0 ];

		return $this -> currentDataSet;
		//return $this -> currentDataSet = new ModelTableResult( $db -> query( $this -> constructQuery() ) );
	}
	
	/**
	 * Constructs query based on current relation
	*/
	function constructQuery() {
		$q = "SELECT " . ( $this -> relation[ 'select' ] ) . " FROM " . ( $this -> tableName ) . ( $this -> generateWhere() ) . ( $this -> relation[ "group" ] ) . ( $this -> relation[ 'having' ] ) . ( $this -> generateOrderBy() ) . ( $this -> generateLimit() );
		
		return $q . ';';
	}
	
	/**
	 * Generates order by part of query based on current relation
	*/
	function generateOrderBy() {
		$o = $this -> relation[ 'order' ];
		if( $o != '' ) $o = ' ORDER BY ' . $o[ 0 ] . ' ' . ( $o[ 1 ] ? 'asc' : 'desc' );
		
		return $o;
	}
	
	/**
	 * Generates limit part of query based on current relation
	*/
	function generateLimit() {
		if( $this -> relation[ 'limit' ][ 0 ] != -1 )
			return ' LIMIT ' . implode( ',', $this -> relation[ 'limit' ] );
		else
			return '';
	}

	/**
	 * Generates where part of query based on current relation
	*/
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
	
	function valid( $row ) {
		foreach( $this -> validations as $field => $validations )
			foreach( $validations as $validation )
				if( call_user_func_array( array( $this, 'validation_' . $validation[ 'rule' ] ), array( $row[ $field ] ) ) === false )
					$this -> errors[] = array( $field, $validation[ 'message' ] );
		
		return empty( $this -> errors );
	}
	
	function validation_required( $val ) {
		return !empty( $val );
	}

	function values( $array ) {
		foreach( $array as $id => $val )
			$this -> $id = $val;

		return $this;
	}
	
	function save() {
		global $db;
		

		if( $this -> newRecord ) {
			if( !$this -> valid( $this -> currentDataSet -> row ) ) return $this;			
			
			$columns = '`' . implode( '`,`', array_keys( $this -> currentDataSet -> row ) ) . '`';
			$values = '';
		
			foreach( $this -> currentDataSet -> row as $id => $val )
				$values .= ( isset( $values[ 0 ] ) ? ',' : '' ) . ( $db -> safe( $val ) );
			
			$db -> query( "INSERT INTO " . ( $this -> tableName ) . " ( " . $columns . " ) VALUES ( " . $values . " )" );
		}	else {
			if( !$this -> valid( $this -> update ) ) return $this;			

			$q = "UPDATE " . ( $this -> tableName ) . " SET "; 
			$first = true;
			
			foreach( $this -> update as $id => $val ) {
				if( $id == "id" ) continue; // TEMP
				if( !$first ) { $q .= ", "; }
				else $first = false;
			
				$q .= '`' . $id . '` = ' . ( $db -> safe( $val ) );
			}
		
			$q .= ( $this -> generateWhere() ) . ( $this -> generateOrderBy() ) . ( $this -> generateLimit() );
			$db -> query( $q );
		}		
		
		return $this;
	}
	
	function delete() {
		global $db;
		$db -> query( "DELETE FROM " . ( $this -> tableName ) . ( $this -> generateWhere() ) );
	}
	
	function dropAndCreateTable() {
		global $db;
		
		$db -> query( "DROP TABLE IF EXISTS " . $this -> tableName );
		
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
		
		$db -> query( $q );
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
