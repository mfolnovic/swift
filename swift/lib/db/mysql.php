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
 * Swift Database Class - LDAP
 *
 * Gives Mysql functionalities to Swift
 *
 * @package			Swift
 * @subpackage	Database
 * @author			Swift dev team
 */

class Db_Mysql extends Base {
	var $conn = NULL;
	var $last_query;
	var $numrows;
	var $options;

	/**
	 * Constructor
	 * @access	public
	 * @param		string	options	Options from configuration file
	 * @return	void
	 */
	function __construct( $options ) {
		$this -> options = $options;
	}

	/**
	 * Destructor 
	 * Disconnects from mysql database
	 * @access	public
	 * @return	void
	 */
	function __destruct() {
		if( $this -> conn ) 
			$this -> conn -> close();
	}

	/**
	 * Secures string from sql injection
	 * @access	public
	 * @param		string	string	String to secure
	 * @return	string
	 */
	function safe( $string ) {
		if( empty( $string ) ) return "''";
		if( $string[ 0 ] == '`' ) return $string;
		if( !$this -> conn ) $this -> connect();
		if( !is_numeric( $string ) ) $string = "'" . mysqli_real_escape_string( $this -> conn, $string ) . "'";

		return $string;
	}

	/**
	 * Connects to mysql database
	 * @access	public
	 * @return	void
	 */
	function connect() {
		$this -> conn = new mysqli( $this -> options[ 'host' ], $this -> options[ 'username' ], $this -> options[ 'password' ], $this -> options[ 'database' ] );
		$this -> conn -> set_charset( 'utf8' );
	}

	/**
	 * Runs query to mysql database
	 * @access	public
	 * @param		string	query	Query to run
	 * @return	resource
	 */
	function query( $query ) {
		if( !$this -> conn ) $this -> connect();

		Benchmark::start( "[SQL $query]" );
		$resource = $this -> conn -> query( $query );

		if( $resource === FALSE ) trigger_error( $this -> conn -> error );
		Benchmark::end( "[SQL $query]" );
		return $resource;
	}

	/**
	 * Generates where part of query based on current relation
	 * @access	public
	 * @param		object	base	Model
	 */
	function generateWhere( &$base ) {
		$ret = '';
		foreach( $base -> relation[ 'where' ] as $id => $val )
			$ret .= ( $ret == '' ? ' WHERE ' : ' AND ' ) . '`' . $id . '`' . $this -> value( $val );
		return $ret;
	}

	/**
	 * Generates parts of query: limit, groupby, order
	 * @access	public
	 * @todo		Implement it
	 */
	function generateExtra( &$base ) {
		return '';
	}

	/**
	 * Does query based on current relation
	 * @access	public
	 * @param		base	Model
	 * @return	void
	*/
	function select( &$base ) {
		if( $base -> relationChanged === FALSE ) return;
		
		$base -> resultSet = array();
		$base -> relationChanged = FALSE;
		$table =& Model::getInstance() -> tables[ $base -> tableName ];
		$relation =& $base -> relation;

		if( empty( $relation[ 'select' ] ) ) $select = '*';
		else $select = 'id,' . implode( ',', $relation[ 'select' ] );

		$res = $this -> query( "SELECT " . $select . " FROM " . $base -> tableName . $this -> generateWhere( $base ) . $this -> generateExtra( $base ) . ';' );

		for( $i = 0; $i < $res -> num_rows; ++ $i ) {
			$row = $res -> fetch_assoc();

			$table[ $row[ 'id' ] ] = new Model_Row( $row );
			$base -> resultSet[ $row[ 'id' ] ] = &$table[ $row[ 'id' ] ];
		}
		
		$base -> handleAssociations();
	}

	/**
	 * Saves changes to Mysql
	 * @access	public
	 * @param		string	base	Model
	 * @return	void
	 */
	function save( &$base ) {
		if( !empty( $base -> newRecord ) ) {
			$columns = '';
			$values = '';

			foreach( $base -> newRecord as $id => $val ) {
				if( $values != '' ) {
					$columns	.= ',';
					$values		.= ',';
				}
				
				$columns	.= "`$id`";
				$values		.= $this -> safe( $val );
			}

			$this -> query( "INSERT INTO {$base -> tableName} ($columns) VALUES ($values)" );
			$base -> newRecord -> id = $this -> conn -> insert_id;
			
			$table =& Model::getInstance() -> tables[ $base -> tableName ];
			$table[ $base -> newRecord -> id ] = $base -> newRecord;
			$base -> resultSet[ $base -> newRecord -> id ] = & $table[ $base -> newRecord -> id ];
			$base -> newRecord = FALSE;
		} else {
			$set = '';
			foreach( $base -> update as $id => $val ) 
				$set .= ( $set == '' ? '' : ',' ) . "`$id`=" . $this -> safe( $val );
			$this -> query( "UPDATE {$base -> tableName} SET $set " . $this -> generateWhere( $base ) );
		}
	}

	/**
	 * Deletes row
	 * @access	public
	 * @param		string	base	Model
	 * @return	void
	 */
	function delete( &$base ) {
		$this -> query( "DELETE FROM {$base -> tableName}" . $this -> generateWhere( $base ) );
	}

	function dropAndCreateTable( &$base ) {
		$this -> query( "DROP TABLE IF EXISTS " . $base -> tableName );
		
		$q = "CREATE TABLE " . $base -> tableName . " (";
		$first = true;
		foreach( $base -> schema as $field => $desc ) {
			if( !$first ) $q .= ',';
			
			$q .= '`' . $field . '` ' . $desc[ 'type' ];
			if( isset( $desc[ 'size' ] ) ) $q .= '(' . $desc[ 'size' ] . ')';
			if( isset( $desc[ 'default' ] ) ) $q .= ' DEFAULT ' . $desc[ 'default' ];
			if( isset( $desc[ 'auto_increment' ] ) ) $q .= ' AUTO_INCREMENT';
			$first = FALSE;
		}
		
		foreach( $base -> schema_keys as $field => $type ) {
			$q .= ',';
			if( $type == 'primary' ) $q .= "PRIMARY KEY (`$field`)";
			// add support for other types of keys
			$first = FALSE;
		}
		$q .= ') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;';
		
		$this -> query( $q );
	}

	/**
	 * Generates values based on type of object
	 * @access	public
	 * @param		string	object	Object
	 * @return	return
	 * <code>
	 * value( '123' ) => " = '123'"
	 * value( "'hello'" ) => " = \'hello\'"
	 * value( array( 1, 2, 3 ) ) => " IN (1,2,3)"
	 * </code>
	 */
	protected function value( $o ) {
		if( !is_array( $o ) ) return " = " . $this -> safe( $o );
		else return " IN ( " . implode( ',', $o ) . " )";
	}
}

?>