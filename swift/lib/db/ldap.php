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
		if( !empty( $this -> conn ) ) return;

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
		$count = count( $table );
		$base -> resultSet = array();
		
		if( empty( $base -> relation[ 'basedn' ] ) ) {
			$basedn = $this -> options[ 'dn' ];
		} else {
			$basedn = $base -> relation[ 'basedn' ][ 0 ];
		}

		$q = $this -> toQuery( $base );

		Benchmark::start( 'query' );
		$entries = $this -> cache -> get( "$basedn|$q" );

		if( $entries === false ) {
			if( !$this -> conn ) $this -> connect();
			$res = @ldap_search( $this -> conn, $basedn, $q, $base -> relation[ 'select' ] );

			if( $res !== false ) {
				$rows = @ldap_get_entries( $this -> conn, $res );

				for( $i = 0; $i < $rows[ 'count' ]; ++ $i ) {
					$entry = array();
					foreach( $rows[ $i ] as $id => $val ) {
						if( !is_numeric( $id ) && $id != 'count' ) {
							if( $val[ 'count' ] == 1 ) $val = $val[ 0 ];
							else if( is_array( $val ) ) array_shift( $val );
						
							$entry[ $id ] = $val;
						}
					}

					$table[ $count + $i ] = new Model_Row( get_class( $base ), $entry );
					$base -> resultSet[ $i ] = &$table[ $count + $i ];
				}
			}
		} else {
			foreach( $entries as $id => $val ) {
				$table[ $count + $id ] = $val;
				$base -> resultSet[ $id ] = &$table[ $count + $id ];
			}
		}

		Log::write( $q, 'LDAP', 'query' );
		$this -> cache -> set( "$basedn|$q", $base -> resultSet, $this -> options[ 'cache' ] );
	}

	/**
	 * Generates conditions
	 *
	 * @access public
	 * @param  object $base Model
	 * @return string
	 */
	function toQuery( &$base ) {
		if( empty( $base -> relation[ 'where' ] ) ) return '(cn=*)';
		
		$where = '';
		$cnt   = 0;
		foreach( $base -> relation[ 'where' ] as $field => $value ) {
			++ $cnt;
			if( !is_array( $value ) ) $value = array( $value );
			foreach( $value as $val ) {
				if( is_numeric( $field ) ) $where .= "($val)";
				else $where .= "($field=$val)";
			}
		}

		if( $cnt > 0 ) $where = "(|$where)";

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

			if( !$this -> conn ) $this -> connect();
			$this -> bindAsAdmin();

			foreach( $base -> update as $id => $val ) {
				if( $id == 'unicodePwd' ) $val = $this -> encryptPassword( $val );
				if( empty( $val ) ) ldap_mod_del( $this -> conn, $base -> dn, array( $id => $val ) );
				else if( $base -> $id === NULL ) {
					@ldap_mod_add( $this -> conn, $base -> dn, array( $id => $val ) );
					if( ldap_errno( $this -> conn ) > 0 ) {
						ldap_mod_replace( $this -> conn, $base -> dn, array( $id => $val ) );
					}
				}
			}

			$base -> resultSet = array();
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

		$ret = !empty( $data[ 0 ] ) && !empty( $data[ 1 ] ) && @ldap_bind( $this -> conn, $data[0] . '@' . $this -> options[ 'domain' ], $data[ 1 ] ) == true;
		ldap_unbind( $this -> conn ); unset( $this -> conn );
		$this -> connect();
		return $ret;
	}

	/**
	 * Encrypt password
	 *
	 * @access  public
	 * @param   string $password Password to encrypt
	 * @return  string
	 */
	function encryptPassword( $password ) {
		$password = "\"" . $password . "\"";
		$ret = '';

		for( $i = 0, $len = strlen( $password ); $i < $len; ++ $i )
			$ret .= "{$password{$i}}\000";
//		$ret = str_replace("\n", "", shell_exec("echo -n '\"" . $password . "\"' | recode latin1..utf-16le/base64"));;
//echo $ret;exit;
		return $ret;
	}
}

?>
