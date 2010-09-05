<?php

/**
 * Swift
 *
 * @author    Swift dev team
 * @copyright Copyright (c) 2010, Swift dev team
 * @license   LICENSE
 * @package   Swift
 */

/**
 * Swift Database Class - LDAP
 *
 * Gives LDAP functionalities to Swift
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Database
 */

class Db_Ldap extends Base {
	/**
	 * Connection to LDAP
	 */
	var $conn = NULL;
	/**
	 * Options in configuration file
	 */
	var $options;
	/**
	 * Cache instance use internally
	 */
	var $cache;

	/**
	 * Constructor
	 * @access public
	 * @param  array options Options from configuration file
	 * @return void
	 */
	function __construct( $options ) {
		$this -> options = $options;
		$this -> cache = Cache::factory( $this -> options[ 'cache_store' ] );
	}

	/**
	 * Destructor - Disconnects from LDAP
	 *
	 * @access public
	 * @return void
	 */
	function __destruct() {
		if( !empty( $this -> conn ) )
			ldap_close( $this -> conn );
	}

	/**
	 * Connects to LDAP and binds as admin
	 *
	 * @access public
	 * @return void
	 * @see bindAdmin
	 */
	function connect() {
		$this -> conn = @ldap_connect( $this -> options[ 'host' ], $this -> options[ 'port' ] );

		if( $this -> conn === FALSE ) trigger_error( "Failed to connect to LDAP server {$this -> options[ 'host' ]}:{$this -> options[ 'port' ]}!", ERROR );

		ldap_set_option( $this -> conn, LDAP_OPT_PROTOCOL_VERSION, 3 );
		ldap_set_option( $this -> conn, LDAP_OPT_REFERRALS, 0 );
		$this -> bindAdmin();
	}

	/**
	 * Binds as username/passwords specified in config
	 *
	 * @access public
	 * @return void
	 */
	function bindAdmin() {
		if( @ldap_bind( $this -> conn, $this -> options[ 'username' ], $this -> options[ 'password' ] ) === FALSE ) 
			trigger_error( "Ldap bind as admin was unsuccessful!", ERROR );
	}

	/**
	 * Queries LDAP with conditions based on current relation
	 *
	 * @access public
	 * @param  object $base Model
	 * @return void
	 */
	function select( &$base ) {
		$table = &Model::instance() -> tables[ $base -> tableName ];
		$base -> resultSet = array();
		$q = $this -> toQuery( $base );

		Benchmark::start( 'query' );
		$entries = $this -> cache -> get( $q );
		var_dump( $entries );exit;
		if( $entries === false ) {
			if( !$this -> conn ) $this -> connect();

			$res = ldap_search( $this -> conn, $this -> options[ 'dn' ], $q );

			if( $res === false ) return $base -> resultSet;
			$entries = @ldap_get_entries( $this -> conn, $res );

			for( $i = 0; $i < $entries[ 'count' ]; ++ $i ) {
				$entry = array();
				foreach( $entries[ $i ] as $id => $val ) {
					if( !is_numeric( $id ) && $id != 'count' ) {
						if( $val[ 'count' ] == 1 ) $val = $val[ 0 ];
						else if( is_array( $val ) ) array_shift( $val );
					
						$entry[ $id ] = $val;
					}
				}

				$table[ $i ] = new Model_Row( get_class( $base ), $entry );
				$base -> resultSet[ $i ] = &$table[ $i ];
			}
		} else {
			foreach( $entries as $id => $val ) {
				$table[ $id ] = $val;
				$base -> resultSet[ $id ] = &$table[ $id ];
			}
		}
		
		Log::write( $q, 'LDAP', 'query' );
		$this -> cache -> set( $q, $base -> resultSet, Config::instance() -> get( 'database', 'ldap', 'cache' ) );
	}

	/**
	 * Generates conditions
	 *
	 * @access public
	 * @param  object $base Model
	 * @return string
	 */
	function toQuery( &$base ) {
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
	 *
	 * @access public
	 * @param  string $base Model
	 * @return void
	 */
	function save( &$base ) {
		global $cache;
		if( !$this -> conn ) $this -> connect();

		if( !$base -> relationChanged ) {
			print_r( $base -> currentDataSet );
		} else {
			$this -> select( $base );
			$this -> cache -> delete( $this -> toQuery( $base ) );
			ldap_modify( $this -> conn, $base -> dn, $base -> update );
		}
	}

	/**
	 * Tries to authenticate with username and password in $data, and returns TRUE if it succeeded, FALSE if it didn't
	 *
	 * @access public
	 * @param  string $base Model
	 * @param  array  $data Username and password
	 * @return bool
	 */
	function authenticate( &$base, $data ) {
		if( !$this -> conn ) $this -> connect();

		return !empty( $data[ 0 ] ) && !empty( $data[ 1 ] ) && @ldap_bind( $this -> conn, $data[0] . '@' . $this -> options[ 'domain' ], $data[ 1 ] ) == true;
	}
}

?>
