<?php

/**
 * Swift
 *
 * @package		Swift
 * @author		Swift dev team
 * @copyright	Copyright (c) 2010, Swift dev team
 * @license		LICENSE
 */

/**
 * Swift Model Class - Base
 *
 * Every application model inherits this class, which allows querying database in simpler way
 *
 * Example:
 * <code>
 * <?php
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
 * ?>
 * </code>
 *
 * @package			Swift
 * @subpackage	Model
 * @author			Swift dev team
 * @todo				__set_state?
 */

class Model_Base extends Base implements IteratorAggregate {
	var $tableName;
	var $resultSet = array();
	var $update = array();
	var $validations = array();
	var $relation = array( 
		'where' => array(), 
		'order' => array(), 
		'select' => array(), 
		'limit' => array(), 
		'group' => array(),
		'having' => array(), 
		'includes' => array(),
		'join' => array()
	);
	var $schema;
	var $hasOne = array();
	var $hasMany = array();
	var $connection = 'default';
	var $link;
	var $newRecord = false;
	var $relationChanged = true;

	/**
	 * Constructor
	 * @access	public
	 * @param		string	tableName	Name of the table
	 * @param		mixed		Array			if it's new row, and NULL if it's not
	 * @return	void
	 */
	function __construct( $tableName, $newRow = NULL ) {
		if( empty( $this -> tableName ) ) $this -> tableName = $tableName;
		$this -> link = DB::factory( $this -> connection );

/*		if( $this -> connection == 'default' ) {
			$res = $this -> link -> query( "SHOW TABLES LIKE '%{$this -> tableName}%'" );
			if( $res -> num_rows == 0 ) $this -> link -> dropAndCreateTable( $this );
		}*/

		if( !empty( $newRow ) )	$this -> newRecord = new Model_Row( $newRow );
	}

	/**
	 * Provides model getter
	 * @access	public
	 * @param		string	key	Key to get
	 * @return	mixed
	 */
	function __get( $key ) {
		if( empty( $this -> resultSet ) && $this -> relationChanged ) $this -> first();
		if( isset( $this -> hasOne[ $key ] ) || isset( $this -> hasMany[ $key ] ) ) $this -> handleAssociation( $key ); // workaround

		reset( $this -> resultSet );
		$tmp = current( $this -> resultSet );
		if( !isset( $tmp -> $key ) ) return NULL;
		return $tmp -> $key;
	}

	/**
	 * Provides model setter
	 * @access	public
	 * @param		string	key		Key to set
	 * @param		mixed		value	New value
	 * @return	void
	 */
	function __set( $key, $value ) {
		reset( $this -> resultSet );
		$row_id = key( $this -> resultSet );
		if( $row_id === NULL ) $this -> resultSet[ $row_id ] = new Model_Row;
		$this -> resultSet[ $row_id ] -> $key = $value;
		$this -> update[ $key ] = & $this -> resultSet[ $row_id ] -> $key;
	}

	/**
	 * Allows calls like find_by_id, find_by_title and changing relation like $model -> where( ... ) etc.
	 * @access	public
	 * @param		string	function	Function name
	 * @param		array		arguments	Passed arguments to this function
	 * @return	return
	 */
	function __call( $function, $arguments ) {
		if( parent::__call( $function, $arguments ) ) return $this;

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

	/**
	 * Allows iterating through model
	 * @access	public
	 * @return	object
	 */
	function getIterator() {
		return new ArrayIterator( $this -> all() );
	}
	/**
	 * Returns first row based on current relation
	 * @access	public
	 * @return	array
	 */
	function first() {
		$this -> limit( 1 );
		$this -> link -> select( $this );

		return reset( $this -> resultSet );
	}

	/**
	 * Returns last row based on current relation
	 * @access	public
	 * @return	array
	 */
	function last() {
		$this -> order( 'id', 'desc' ) -> limit( 1 );
		$this -> link -> select( $this );

		return reset( $this -> resultSet );
	}

	/**
	 * Returns all rows based on current relation
	 * @access	public
	 * @return	array
	 */
	function all() {
		$this -> link -> select( $this );

		return $this -> resultSet;
	}

	/**
	 * Changes current row values
	 * But, it doesn't save that row, you need to call save() to save it.
	 * <code>
	 * <?php
	 * $this -> model( 'articles' ) -> find_by_id( 5 ) -> values( array( 'title' => 'New title', 'content' => 'New content' ) );
	 * ?>
	 * </code>
	 * @access	public
	 * @param		array	values	Values to change
	 * @return	object
	 */
	function values( $values ) {
		foreach( $values as $id => $val )
			$this -> $id = $val;

		return $this;
	}

	/**
	 * Handles associations has_many, has_one
	 * @access	public
	 * @return	object
	 */
	function handleAssociations() {
		foreach( $this -> relation[ 'includes' ] as $name => $assoc ) {
			if( is_numeric( $name ) ) { $name = $assoc; $assoc = NULL; }
			$this -> handleAssociation( $name, $assoc );
		}

		return $this;
	}

	/**
	 * Handles specific association
	 * @access	public
	 * @param		string	name	Name of association
	 * @param		bool		assoc	TRUE if called from association
	 * @return	void
	 * @todo		Avoid $assoc
	 */
	function handleAssociation( $name, $assoc = NULL ) {
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

		$assocModel = Model::instance() -> create( $className ) -> where( array( $association[ 'foreignKey' ] => array_keys( $ids ) ) );
		if( !empty( $assoc ) ) $assocModel -> includes( $assoc );

		foreach( $this -> resultSet as $id => $val ) {
			$val -> $name = new $className( $className );
			$val -> $name -> relationChanged = false;
		}

		foreach( $assocModel -> all() as $id => $row )
			foreach( $ids[ $row -> $foreignKey ] as $key => $dataID )
				$this -> resultSet[ $dataID ] -> $name -> resultSet[ $id ] = $row;
	}
}

?>
