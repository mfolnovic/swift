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
 * Gives LDAP functionalities to Swift
 *
 * @package			Swift
 * @subpackage	Database
 * @author			Swift dev team
 */

class Db_Ldap extends Base {
	var $conn = NULL;
	var $options;
	var $cache;

	/**
	 * Constructor
	 * @access	public
	 * @param		options	Options from configuration file
	 * @return	void
	 */
	function __construct( $options ) {
		$this -> options = $options;
		$this -> cache = Cache::getInstance( $this -> options[ 'cache_store' ] );
	}

	/**
	 * Destructor - Disconnects from LDAP
	 * @access	public
	 * @return	void
	 */
	function __destruct() {
		if( !empty( $this -> conn ) )
			ldap_close( $this -> conn );
	}

	/**
	 * Connects to LDAP and binds as admin
	 * @see bindAdmin
	 * @access	public
	 * @return	void
	 */
	function connect() {
		$this -> conn = @ldap_connect( $this -> options[ 'host' ], $this -> options[ 'port' ] );

		if( $this -> conn === FALSE ) trigger_error( "Ldap connection failed!" );

		ldap_set_option( $this -> conn, LDAP_OPT_PROTOCOL_VERSION, 3 );
		ldap_set_option( $this -> conn, LDAP_OPT_REFERRALS, 0 );
		$this -> bindAdmin();
	}

	/**
	 * Binds as username/passwords specified in config
	 * @access	public
	 * @return	void
	 */
	function bindAdmin() {
		if( @ldap_bind( $this -> conn, $this -> options[ 'username' ], $this -> options[ 'password' ] ) === FALSE ) 
			trigger_error( "Ldap bind as admin was unsuccessful!" );
	}

	/**
	 * Queries LDAP with conditions based on current relation
	 * @access	public
	 * @param		object	base	Model
	 * @return	void
	 */
	function select( &$base ) {
		global $config, $model;

		$table = &$model -> tables[ $base -> tableName ];
		$base -> resultSet = array();
		$q = $this -> generateConditions( $base );

		Benchmark::start( 'query' );
		$entries = $this -> cache -> get( $q );
		if( $entries === false ) {
			if( !$this -> conn ) $this -> connect();

			$res = ldap_search( $this -> conn, $this -> options[ 'dn' ], $q );

			if( $res === false ) return $base -> resultSet;
			$entries = ldap_get_entries( $this -> conn, $res );

			for( $i = 0; $i < $entries[ 'count' ]; ++ $i ) {
				$entry = array();
				foreach( $entries[ $i ] as $id => $val ) {
					if( !is_numeric( $id ) && $id != 'count' ) {
						if( $val[ 'count' ] == 1 ) $val = $val[ 0 ];
						else if( is_array( $val ) ) array_shift( $val );
					
						$entry[ $id ] = $val;
					}
				}

				$table[ $i ] = new Model_Row( $entry );
				$base -> resultSet[ $i ] = &$table[ $i ];
			}
		} else {
			foreach( $entries as $id => $val ) {
				$table[ $id ] = $val;
				$base -> resultSet[ $id ] = &$table[ $id ];
			}
		}
		
		Log::write( $q, 'LDAP', 'query' );
		$this -> cache -> set( $q, $base -> resultSet, $config -> options[ 'database' ][ 'ldap' ][ 'cache' ] );
	}

	/**
	 * Generates conditions
	 * @access	public
	 * @param		object	base	Model
	 * @return	string
	 */
	function generateConditions( &$base ) {
		if( empty( $base -> relation[ 'where' ] ) ) return '(webid=*)';
		
		$where = '';
		foreach( $base -> relation[ 'where' ] as $field => $value ) {
			if( !is_array( $value ) ) $value = array( $value );
			foreach( $value as $val ) {
				if( is_numeric( $field ) ) $where .= "($val)";
				else $where .= "($field=$val)";
			}
		}

		return $where;
	}

	/**
	 * Saves changes to LDAP
	 * @access	public
	 * @param		string	base	Model
	 * @return	void
	 */
	function save( &$base ) {
		global $cache;
		if( !$this -> conn ) $this -> connect();

		if( $base -> newRecord ) {
			print_r( $base -> currentDataSet );
		} else {
			$this -> select( $base );
			$this -> cache -> delete( $this -> generateConditions( $base ) );
			ldap_modify( $this -> conn, $base -> dn, $base -> update );
		}
	}

	/**
	 * Tries to authenticate with username and password in $data, and returns TRUE if it succeeded, FALSE if it didn't
	 * @access	public
	 * @param		string	base	Model
	 * @param		array		data	Username and password
	 * @return	bool
	 */
	function authenticate( &$base, $data ) {
		if( !$this -> conn ) $this -> connect();

		return !empty( $data[ 0 ] ) && !empty( $data[ 1 ] ) && @ldap_bind( $this -> conn, $data[0], $data[ 1 ] ) == true;
	}
}

?>
