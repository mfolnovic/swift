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
 * Swift Security Class
 *
 * This class is responsible for filtering data from XSS, checking if it's CSRF attack etc.
 *
 * @package			Swift
 * @subpackage	Security
 * @author			Swift dev team
 */


class Security {
	static $instance = NULL;
	var $csrf_token;
	var $token_expiration = 3600; // 1 hour

	/**
	 * Constructor
	 * @access	public
	 * @return	void
	 */
	function __construct() {
		$this -> filter( $_POST );
		$this -> CsrfToken();
	}

	/**
	 * Checks if CSRF token is correct
	 * @access	public
	 * @return	void
	 */
	function checkCsrf() {
		if( empty( $_POST ) ) return;

		$time = time();
		foreach( $_SESSION[ 'csrf_tokens' ] as $id => $val )
			if( $val[ 1 ] - $time > $this -> token_expiration )
				unset( $_SESSION[ 'csrf_tokens' ][ $id ] );
			else if( $val[ 0 ] == $_POST[ 'csrf_token' ] )
				return;

		Controller::getInstance() -> render404();
	}

	/**
	 * This function generates csrf token
	 * @access	public
	 * @return	void
	 */
	function CsrfToken() {
		$this -> csrf_token = md5( mt_rand() );

		if( !isset( $_SESSION[ 'csrf_tokens' ] ) ) $_SESSION[ 'csrf_tokens' ] = array();
		array_push( $_SESSION[ "csrf_tokens" ], array( $this -> csrf_token, time() ) );
	}

	/**
	 * Fitlers array $array from XSS
	 * @access	public
	 * @param		array	$array	Array to filter
	 * @return	array
	 */
	function filter( &$array ) {
		if( is_string( $array ) ) return $array = htmlentities( $array );

		foreach( $array as $id => &$val )
			$this -> filter( $val );
	}

	/**
	 * Singleton
	 * @access	public
	 * @return	object
	 * @static
	 */
	static function instance() {
		if( empty( self::$instance ) ) self::$instance = new Security;
		return self::$instance;
	}
}

?>
