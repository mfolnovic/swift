<?php

class Ldap extends Base {
	var $conn = NULL;
	var $options;

	function connect() {
		$this -> conn = ldap_connect( $this -> options[ 'host' ], $this -> options[ 'port' ] );
		
		ldap_set_option( $this -> conn, LDAP_OPT_PROTOCOL_VERSION, 3 );
		$this -> bindAdmin();
	}
	
	function __destruct() {
		if( !empty( $this -> conn ) )
			ldap_close( $this -> conn );
	}
	
	function bindAdmin() {
		@ldap_bind( $this -> conn, 'uid=' . $this -> options[ 'username' ] . ',' . $this -> options[ 'dn' ], $this -> options[ 'password' ] );
	}
	
	function doQuery( &$model ) {
		if( !$this -> conn ) $this -> connect();

		$res = ldap_search( $this -> conn, $this -> options[ 'dn' ], $this -> generateConditions( $model ) );
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
		
		return $model -> currentDataSet;
	}
	
	function generateConditions( &$model ) {
		if( empty( $model -> relation[ 'where' ] ) ) return '(uid=*)';
		
		$where = '';
		foreach( $model -> relation[ 'where' ] as $field => $value )
			$where .= "($field=$value)";
		
		return $where;
	}
	
	function authenticate( $username, $password ) {
		return !empty( $username ) && !empty( $password ) && @ldap_bind( $this -> conn, "uid=$username,{$this -> options['dn']}", $password ) == true;
	}
}

$ldap = new LDAP;

?>
