<?php

class LDAP extends Base {
	var $conn;

	function __construct() {
		global $config;
		
		$options = &$config -> options[ 'ldap' ];
		$this -> conn = ldap_connect( $options[ 'host' ], $options[ 'port' ] );
		
		ldap_set_option( $this -> conn, LDAP_OPT_PROTOCOL_VERSION, 3 );
		$this -> bindAdmin();
	}
	
	function __destruct() {
		ldap_close( $this -> conn );
	}
	
	function bindAdmin() {
		global $config;
		
		$options = &$config -> options[ 'ldap' ];
		@ldap_bind( $this -> conn, 'uid=' . $options[ 'username' ] . ',' . $options[ 'dn' ], $options[ 'password' ] );
	}
	
	function __call( $function, $arguments ) {
		global $config;
		
		$options = &$config -> options[ 'ldap' ];
		
		if( substr( $function, 0, 7 ) == 'find_by' ) {
			$name = substr( $function, 8 );
			$res = ldap_search( $this -> conn, $options[ 'dn' ], "$name={$arguments[0]}" );
			$entries = ldap_get_entries( $this -> conn, $res );
			
			return $entries;
		}
	}
	
	function authenticate( $username, $password ) {
		global $config;
		
		$options = &$config -> options[ 'ldap' ];
		return @ldap_bind( $this -> conn, "uid=$username,{$options['dn']}", $password ) == true;
	}
}

$ldap = new LDAP;

?>
