<?php

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
 * $user = $this -> model( 'user' ) -> find_by_id( $this -> data[ "id" ] ) -> values( $this -> data ) -> save();
 * 
 * // deleting record
 * $this -> model( 'user' ) -> find_by_id( $this -> data[ "id"]  ) -> delete();
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
 
class ModelBase extends Base {
	/**
		Table name
	*/
	var $tableName;
	/**
		Set of results, but only contains ID, all data is stored in model 
	*/
	var $resultSet = array();
	/**
		Contains IDS of rows that were updated
	*/
	var $update = array();
	/**
		Contains all validation rules
	*/
	var $validations = array();
	
	var $relation = array( 
		'where' => array(), 
		'order' => array(), 
		'select' => array(), 
		'limit' => array(), 
		'group' => array(),
		'having' => array(), 
		'includes' => array() 
	);
	
	var $schema;
	var $hasOne = array();
	var $hasMany = array();
	var $connection = 'default';
	var $link;
	var $newRecord = false;
	var $relationChanged = true;
	
	function __construct( $tableName, $newRow = NULL ) {
		if( empty( $this -> tableName ) ) $this -> tableName = $tableName;
		$this -> link = DB::getInstance( $this -> connection );;

		if( $this -> connection == 'default' ) {
			$res = $this -> link -> query( "SHOW TABLES LIKE '%{$this -> tableName}%'" );
			if( $res -> num_rows == 0 ) $this -> link -> dropAndCreateTable( $this );
		}
		
		if( !empty( $newRow ) )	$this -> newRecord = new ModelRow( $newRow );
	}
	
	function __get( $index ) {
		if( empty( $this -> resultSet ) ) $this -> first();
		if( isset( $this -> hasOne[ $index ] ) ) {
			$this -> handleAssociation( $index ); // workaround
		}
		
		reset( $this -> resultSet );
		$tmp = current( $this -> resultSet );
		if( !isset( $tmp -> $index ) ) return NULL;
		return $tmp -> $index;
	}
	
	function __set( $index, $value ) {
		reset( $this -> resultSet );
		$key = key( $this -> resultSet );
		if( $key === NULL ) $this -> resultSet[ $key ] = new ModelRow();
		$this -> resultSet[ $key ] -> $index = $value;
		$this -> update[ $index ] = & $this -> resultSet[ $key ] -> $index;
	}
	
	function __call( $function, $arguments ) {
		if( in_array( $function, array_keys( $this -> relation ) ) ) {
			if( is_array( $arguments[ 0 ] ) ) $args = $arguments[ 0 ];
			else $args = $arguments;
			
			$this -> relation[ $function ] = $args;
			$this -> relationChanged = true;
		} else if( $function == 'find' || substr( $function, 0, 8 ) == 'find_by_' ) {
			$field = substr( $function, 8 );
			if( empty( $field ) ) $field = 'id';
			
			$this -> relation[ 'where' ] = array_merge( $this -> relation[ 'where' ], array( $field => ( count( $arguments ) == 1 ? $arguments[ 0 ] : $arguments ) ) );
			$this -> relationChanged = true;
		} else if( method_exists( $this -> link, $function ) ) {
			$ret = $this -> link -> $function( $this, $arguments );
			if( $ret !== null ) return $ret;
		}	else trigger_error( "Unknown function $function!" );
		
		return $this;
	}
	
	function first() {
		$this -> relation[ 'limit' ] = array( 1 );
		$this -> link -> select( $this );
		
		return current( $this -> resultSet );
	}
	
	function last() {
		$this -> relation[ 'order' ][] = array( 'id', 'desc' );
		$this -> link -> select( $this );
		
		return current( $this -> resultSet );
	}
	
	function all() {
		$this -> link -> select( $this );
		
		return $this -> resultSet;
	}
	
	function values( $values ) {
		foreach( $values as $id => $val )
			$this -> $id = $val;
			
		return $this;
	}
	
	function handleAssociations() {
		foreach( $this -> relation[ 'includes' ] as $name => $assoc ) {
			if( is_numeric( $name ) ) { $name = $assoc; $assoc = NULL; }
			$this -> handleAssociation( $name, $assoc );
		}
		return $this;
	}
	
	function handleAssociation( $name, $assoc = NULL ) {
		global $model;
		
		if( isset( $this -> hasMany[ $name ] ) ) { $association = &$this -> hasMany[ $name ]; $type = 'hasMany'; }
		else if( isset( $this -> hasOne[ $name ] ) ) { $association = &$this -> hasOne[ $name ]; $type = 'hasOne'; }
		else return false;
		
		$association = array_merge( array( 'primaryKey' => 'id', 'foreignKey' => 'id' ), $association );
		
		$className = $association[ 'model' ];
		$primaryKey = $association[ 'primaryKey' ];
		$foreignKey = $association[ 'foreignKey' ];

		$ids = array();
		foreach( $this -> resultSet as $id => $row ) {
			if( !isset( $ids[ $row -> $primaryKey ] ) ) $ids[ $row -> $primaryKey ] = array();
			
			$ids[ $row -> $primaryKey ][] = $id;
		}

		$assocModel = $model -> create( $className ) -> where( array( $association[ 'foreignKey' ] => array_keys( $ids ) ) );
		if( !empty( $assoc ) ) $assocModel -> includes( $assoc );
	
		foreach( $this -> resultSet as $id => $val ) {
			$val -> $name = new $className( $className );
			$val -> $name -> newRecord = false;
		}

		foreach( $assocModel -> all() as $id => $row )
			foreach( $ids[ $row -> $foreignKey ] as $key => $dataID )
				$this -> resultSet[ $dataID ] -> $name -> resultSet[ $id ] = $row;
	}
}

?>
