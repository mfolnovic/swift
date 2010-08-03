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
		echo "<script type=\"text/javascript\" src=\"/{$opt[ 'url_prefix' ]}all$version.js\"></script>";
	else {
		$ret = '';
		foreach( $args as $val )
			$ret .= "<script type=\"text/javascript\" src=\"/{$opt[ 'url_prefix' ]}$val\"></script>";
		echo $ret;
	}
}	

function stylesheet() {
	global $config;
	$opt =& $config -> options[ 'other' ];
	$version = !isset( $opt[ 'static_version' ] ) || $opt[ 'static_version' ] === false ? '' : '.' . $opt[ 'static_version' ];

	if( $version != '' && file_exists( PUBLIC_DIR . 'stylesheets/all.css' ) )
		echo "<link href=\"/{$opt[ 'url_prefix' ]}all$version.css\" rel=\"stylesheet\" type=\"text/css\">";
	else {
		$ret = '';

		foreach( func_get_args() as $val )
			$ret .= "<link href=\"/{$opt[ 'url_prefix' ]}$val\" rel=\"stylesheet\" type=\"text/css\">";
			echo $ret;
	}
}

function favicon( $icon ) {
	global $config;
	echo '<link rel="icon" href="/' . ( $config -> options[ 'other' ][ 'url_prefix' ] ) . 'favicon.ico">';
}

function image( $image, $options = array() ) {
	global $config;
	if( strpos( $image, '/' ) === false ) $image = "images/$image";
	$options = _attributes( $options );
	echo "<img src=\"/{$config -> options[ 'other' ][ 'url_prefix' ]}/$image\" $options>";
}

function format_time( $timestamp ) {
	global $config;
	echo date( $config -> options[ 'other' ][ 'format_date' ], $timestamp );
}

function form( $url, $options = array() ) {
	global $controller, $config;
	echo "<form action=\"/" . ( $config -> options[ 'other' ][ 'url_prefix' ] ) . "$url\" " . _attributes( $options ) . '><input type="hidden" name="csrf_token" value="' . $controller -> instance -> csrf_token . '">';
}

function formEnd() {
	echo "</form>";
}

function link_tag( $title, $href, $options = array() ) {
	global $config;
	$options = _attributes( $options );
	echo '<a href="/' . ( $config -> options[ 'other' ][ 'url_prefix' ] ) . str_replace( " ", "+", $href ) . '" ' . $options . '>' . $title . '</a>';
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
