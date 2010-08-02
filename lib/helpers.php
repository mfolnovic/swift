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

?>
