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
 * Tests if this request was requested by ajax
 * Currently only works with javascripts frameworks (jquery tested only)
 * @return	bool
 * @todo		Doesn't work while file upload using malsup form plugin
 */
function isAjax() {
	return isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) === 'xmlhttprequest';
}

/**
 * Returns same prefixed from $str1 and $str2
 * @param		string	str1	First string
 * @param		string	str2	Second string
 * @return	string
 */
function samePrefix( $str1, $str2 ) {
	$ret = '';

	for( $i = 0, $len1 = strlen( $str1 ), $len2 = strlen( $str2 ); $i < $len1 && $i < $len2 && $str1[ $i ] == $str2[ $i ]; ++ $i )
		$ret .= $str1[ $i ];

	return $ret;
}

/**
 * Removes same prefix of $str1 and $str2 from $str1 and returns it
 * @param		string	str1	First string
 * @param		string	str2	Second string
 * @return	string
 */
function removeSamePrefix( $str1, $str2 ) {
	return substr( $str1, 0, strlen( samePrefix( $str1, $str2 ) ) );
}

/**
 * For a given filename, returns file extension
 * @access	public
 * @param		string	filename	Name of file
 * @return	return
 */
function extension( $filename ) {
	return substr( $filename, - strpos( '.', $filename ) );
}

function get_parent_classes( $class ) {
	$ret = array( $class );

	while( ( $class = get_parent_class( $class ) ) !== FALSE )
		$ret[] = $class;

	return $ret;
}

?>
