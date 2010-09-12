<?php

/**
 * Swift
 *
 * @package   Swift
 * @author    Swift dev team
 * @copyright Copyright (c) 2010, Swift dev team
 * @license   LICENSE
 */

/**
 * Swift View Class - Helpers
 *
 * Usual view helpers
 *
 * @package    Swift
 * @subpackage View
 * @author     Swift dev team
 */

function javascript() {
	$version = Config::instance() -> get( 'static_version' );

	if( !empty( $version ) && file_exists( PUBLIC_DIR . 'javascripts/all.js' ) )
		return "<script type=\"text/javascript\" src=\"" . URL_PREFIX . "javascripts/all$version.js\"></script>";
	else {
		$ret = '';

		foreach( func_get_args() as $val )
			$ret .= "<script type=\"text/javascript\" src=\"" . URL_PREFIX . "javascripts/$val\"></script>";

		return $ret;
	}
}	

function stylesheet() {
	$version = Config::instance() -> get( 'static_version' );

	if( !empty( $version ) && file_exists( PUBLIC_DIR . 'stylesheets/all.css' ) )
		return "<link href=\"" . URL_PREFIX . "stylesheets/all$version.css\" rel=\"stylesheet\" type=\"text/css\">";
	else {
		$ret = '';

		foreach( func_get_args() as $val )
			$ret .= "<link href=\"" . URL_PREFIX . "stylesheets/$val\" rel=\"stylesheet\" type=\"text/css\">";

		return $ret;
	}
}

function favicon( $icon ) {
	return '<link rel="icon" href="' . URL_PREFIX . 'favicon.ico">';
}

function image( $image, $options = array() ) {
	return "<img src=\"" . URL_PREFIX . "$image\" " . _attributes( $options ) . ">";
}

function format_time( $timestamp ) {
	return date( Config::instance() -> get( 'format_date' ), $timestamp );
}

function form( $url, $options = array() ) {
	$options = array_merge( $options, array( 'method' => 'post' ) );
	return "<form action=\"" . URL_PREFIX . "$url\" " . _attributes( $options ) . '><input type="hidden" name="csrf_token" value="' . Controller::instance() -> object -> csrf_token . '">';
}

function _formEnd() {
	return "</form>";
}

function ahref( $title, $href, $options = array() ) {
	return '<a href="' . URL_PREFIX . str_replace( " ", "+", $href ) . '" ' . _attributes( $options ) . '>' . $title . '</a>';
}

function partial( $name ) {
	View::instance() -> render( null, '_' . $name );
}

function render( $controller = NULL, $action = NULL ) {
	View::instance() -> render( $controller, $action );
}

function xss_clean( $string ) {
	return htmlentities( $string, ENT_QUOTES, 'utf-8' );
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
