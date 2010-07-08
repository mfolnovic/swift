<?php

class Model {
	var $tables = array();
}

class ModelRow {
	var $row;
	
	function __construct( $row ) {
		$this -> row = $row;
	}
	
	function &__get( $id ) {
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
 * $user = $this -> model( 'user', $this -> data[ 'user' ] ) -> save();
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
	var $connection = 'default';
	var $link = NULL;
	var $hasMany = array();
	var $hasOne = array();
		
	/**
	 * ModelBase constructor
	 * @param array $newRecord Contains array of data which will be inserted in db when save() is run on the model 
	*/
	
	function __construct( $newRecord = array() ) {
		global $db;
		
		if( empty( $this -> tableName ) ) $this -> tableName = strtolower( get_class( $this ) );
		$this -> link = & $db -> connections[ $this -> connection ];

		if( $this -> dropAndCreateTable ) $this -> link -> dropAndCreateTable( $this );
		if( !empty( $newRecord ) ) {
			$this -> currentDataSet = new ModelRow( $newRecord );
			$this -> newRecord = true;
		}
		
		$this -> relation = array( 'where' => array(), 'order' => '', 'select' => '*', 'limit' => array( 0 => -1 ), 'group' => '', 'having' => '', 'includes' => array() );
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
		if( empty( $this -> currentDataSet ) ) $this -> currentDataSet = $this -> link -> doQuery( $this );
		if( isset( $this -> currentDataSet -> $name ) ) return $this -> currentDataSet -> $name;
		
		return NULL;
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
		global $model;

		if( empty( $this -> currentDataSet ) ) $this -> currentDataSet = $this -> link -> doQuery( $this ); // temporary
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
		if( substr( $name, 0, 7 ) == 'find_by' ) {
			$name = substr( $name, 8 );
			$this -> where( array( $name => $arguments[ 0 ] ) );
		} else {
			return call_user_func_array( array( $this -> link, $name ), array( &$this, $arguments ) );
		}
		
		return $this;
	}

	/**
	 * Used to get all records for current relation
	*/
	function all() {
		return $this -> link -> doQuery( $this, false );
	}
	
	/**
	 * Used to get first record for current relation
	*/
	function first() {
		$this -> limit( 1 );
	
		return $this -> link -> doQuery( $this );
	}
	
	/**
	 * Used to get last record for current relation
	*/
	function last() {
		$this -> limit( 1 );
		
		$o = $this -> relation[ 'order' ];
		$this -> order( $o[ 0 ], !$o[ 1 ] );
		
		return $this -> link -> doQuery( $this );
	}
	
	/**
	 * Same as find_by_id( 5 )
	*/
	function find( $id ) {
		$this -> where( array( 'ID', $id ) );
		
		return $this -> link -> doQuery( $this );
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
	
	function includes() {
		$this -> relation[ 'includes' ] = array_merge( $this -> relation[ 'includes' ], func_get_args() );
		return $this;
	}
};

$model = new Model;

?>
