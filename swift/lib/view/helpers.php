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
 * Swift View Class - Helpers
 *
 * Usual view helpers
 *
 * @package			Swift
 * @subpackage	View
 * @author			Swift dev team
 */

function javascript() {
	global $config;
	$args = func_get_args();

	$opt =& $config -> options[ 'other' ];
	$version = !isset( $opt[ 'static_version' ] ) || $opt[ 'static_version' ] === false ? '' : '.' . $opt[ 'static_version' ];

	if( $version != '' && file_exists( PUBLIC_DIR . 'javascripts/all.js' ) )
		echo "<script type=\"text/javascript\" src=\"" . URL_PREFIX . "javascripts/all$version.js\"></script>";
	else {
		$ret = '';
		foreach( $args as $val )
			$ret .= "<script type=\"text/javascript\" src=\"" . URL_PREFIX . "javascripts/$val\"></script>";
		echo $ret;
	}
}	

function stylesheet() {
	global $config;
	$opt =& $config -> options[ 'other' ];
	$version = !isset( $opt[ 'static_version' ] ) || $opt[ 'static_version' ] === false ? '' : '.' . $opt[ 'static_version' ];

	if( $version != '' && file_exists( PUBLIC_DIR . 'stylesheets/all.css' ) )
		echo "<link href=\"" . URL_PREFIX . "stylesheets/all$version.css\" rel=\"stylesheet\" type=\"text/css\">";
	else {
		$ret = '';

		foreach( func_get_args() as $val )
			$ret .= "<link href=\"" . URL_PREFIX . "stylesheets/$val\" rel=\"stylesheet\" type=\"text/css\">";
			echo $ret;
	}
}

function favicon( $icon ) {
	global $config;
	echo '<link rel="icon" href="' . URL_PREFIX . 'favicon.ico">';
}

function image( $image, $options = array() ) {
	if( strpos( $image, '/' ) === false ) $image = "images/$image";
	$options = _attributes( $options );
	echo "<img src=\"" . URL_PREFIX . "$image\" $options>";
}

function format_time( $timestamp ) {
	global $config;
	echo date( $config -> options[ 'other' ][ 'format_date' ], $timestamp );
}

function form( $url, $options = array() ) {
	global $controller;
	$options = array_merge( $options, array( 'method' => 'post' ) );
	echo "<form action=\"" . URL_PREFIX . "$url\" " . _attributes( $options ) . '><input type="hidden" name="csrf_token" value="' . $controller -> instance -> csrf_token . '">';
}

function _formEnd() {
	echo "</form>";
}

function ahref( $title, $href, $options = array() ) {
	$options = _attributes( $options );
	echo '<a href="' . URL_PREFIX . str_replace( " ", "+", $href ) . '" ' . $options . '>' . $title . '</a>';
}

function partial( $name ) {
	View::getInstance() -> render( null, '_' . $name );
}

function render( $c = NULL, $a = NULL ) {
	View::getInstance() -> render( $c, $a );
}

function xss_clean( $string ) {
	echo htmlentities( $string, ENT_QUOTES, 'utf-8' );
}

function _attributes( $array ) {
	$ret = '';

	foreach( $array as $id => $val ) {
		if( $ret != '' ) $ret .= ' ';
		$ret .= "$id=\"$val\"";
	}

	return $ret;
}

?>
