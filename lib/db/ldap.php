<?php

class Ldap extends Base {
	var $conn = NULL;
	var $options;
	var $cache;
	
	function __construct( $options ) {
		$this -> options = $options;
		$this -> cache = Cache::getInstance( $this -> options[ 'cache_store' ] );
	}

	function connect() {
		$this -> conn = ldap_connect( $this -> options[ 'host' ], $this -> options[ 'port' ] );

		ldap_set_option( $this -> conn, LDAP_OPT_PROTOCOL_VERSION, 3 );
		ldap_set_option( $this -> conn, LDAP_OPT_REFERRALS, 0 );
		$this -> bindAdmin();
	}
	
	function __destruct() {
		if( !empty( $this -> conn ) )
			ldap_close( $this -> conn );
	}
	
	function bindAdmin() {
//		ldap_bind( $this -> conn, 'cn=' . $this -> options[ 'username' ] . ',' . $this -> options[ 'dn' ], $this -> options[ 'password' ] );
		ldap_bind( $this -> conn, $this -> options[ 'username' ], $this -> options[ 'password' ] );
	}
	
	function select( &$base ) {
		global $config, $model;

		$table = &$model -> tables[ $base -> tableName ];
		$base -> resultSet = array();
		$q = $this -> generateConditions( $base );

		$from_cache = $this -> cache -> get( $q );
		if( $from_cache !== false ) { Log::getInstance() -> write( "[CACHE]: $q" ); return $from_cache; }
		
		Benchmark::start( "[LDAP $q]" );
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

			$table[ $i ] = new ModelRow( $entry );
			$base -> resultSet[ $i ] = &$table[ $i ];
		}
		
		Benchmark::end( "[LDAP $q]" );
		$this -> cache -> set( $q, $base -> resultSet, $config -> options[ 'database' ][ 'ldap' ][ 'cache' ] );
	}
	
	function generateConditions( &$model ) {
		if( empty( $model -> relation[ 'where' ] ) ) return '(webid=*)';
		
		$where = '';
		foreach( $model -> relation[ 'where' ] as $field => $value ) {
			if( !is_array( $value ) ) $value = array( $value );
			foreach( $value as $val ) {
				if( is_numeric( $field ) ) $where .= "($val)";
				else $where .= "($field=$val)";
			}
		}

		return $where;
	}
	
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
	
	function authenticate( &$base, $data ) {
		if( !$this -> conn ) $this -> connect();

 		return !empty( $data[ 0 ] ) && !empty( $data[ 1 ] ) && @ldap_bind( $this -> conn, $data[0], $data[ 1 ] ) == true;
	}
}

?>
