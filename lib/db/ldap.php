<?php

class Ldap extends Base {
	var $conn = NULL;
	var $options;

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
	
	function doQuery( &$model, $options = array( false ) ) {
		global $cache, $config;
		if( $model -> currentDataSet !== null && $model -> relationChanged ) return $model -> currentDataSet;

		$model -> currentDataSet = array();
		$q = $this -> generateConditions( $model );
		
		$from_cache = $cache -> get( $q );
		if( $from_cache !== false ) return $from_cache;
		
		if( !$this -> conn ) $this -> connect();
		$res = ldap_search( $this -> conn, $this -> options[ 'dn' ], $q );

		if( $res === false ) return $model -> currentDataSet;
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
			$model -> currentDataSet[] = new ModelRow( $entry );
		}
			
		if( $options[ 0 ] && $entries[ 'count' ] == 1 ) $model -> currentDataSet = $model -> currentDataSet[ 0 ];

		return $cache -> set( $q, $model -> currentDataSet, $config -> options[ 'ldap' ][ 'cache' ] );
	}
	
	function generateConditions( &$model ) {
		if( empty( $model -> relation[ 'where' ] ) ) return '(uid=*)';
		
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
	
	function save( &$model ) {
		if( !$this -> conn ) $this -> connect();

		if( $model -> newRecord ) {
			print_r( $model -> currentDataSet );
		} else {
			$result = $this -> doQuery( $model );
			ldap_modify( $this -> conn, $result -> dn, $model -> update );
		}
		
		return $model;
	}
	
	function authenticate( $model, $data ) {
		if( !$this -> conn ) $this -> connect();
	
		return !empty( $data[ 0 ] ) && !empty( $data[ 1 ] ) && @ldap_bind( $this -> conn, "uid={$data[0]},{$this -> options['dn']}", $data[ 1 ] ) == true;
	}
}

$ldap = new LDAP;

?>
