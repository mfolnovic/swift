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

/**
 * Generate html for including passed javascript files
 * 
 * @access public
 * @param  string $file1, ..Javascript files
 * @return return
 */
function javascript() {
	if(file_exists(PUBLIC_DIR . 'javascripts/all.js')) {
		$version = filemtime(PUBLIC_DIR . 'javascripts/all.js');
		return "<script type=\"text/javascript\" src=\"" . URL_PREFIX . "javascripts/all.js?$version\"></script>";
	} else {
		$ret = '';

		foreach(func_get_args() as $val) {
			$ret .= "<script type=\"text/javascript\" src=\"" . URL_PREFIX . "javascripts/$val\"></script>";
		}

		return $ret;
	}
}

function stylesheet() {
	if(file_exists(PUBLIC_DIR . 'stylesheets/all.css')) {
		$version = filemtime(PUBLIC_DIR . 'stylesheets/all.css');
		return "<link href=\"" . URL_PREFIX . "stylesheets/all.css?$version\" rel=\"stylesheet\" type=\"text/css\">";
	} else {
		$ret = '';

		foreach(func_get_args() as $val) {
			$ret .= "<link href=\"" . URL_PREFIX . "stylesheets/$val\" rel=\"stylesheet\" type=\"text/css\">";
		}

		return $ret;
	}
}

function favicon($icon) {
	return '<link rel="icon" href="' . URL_PREFIX . 'favicon.ico">';
}

function image($image, $options = array()) {
	return "<img src=\"" . URL_PREFIX . "$image\"" . _attributes($options) . ">";
}

function format_time($timestamp) {
	return date(Config::get('format_date'), $timestamp);
}

function form($url, $options = array()) {
	$options = array_merge($options, array('method' => 'post'));

	return "<form action=\"" . URL_PREFIX . "$url\"" . _attributes($options) . '><input type="hidden" name="csrf_token" value="' . App::$request -> object -> csrf_token . '">';
}

function _formEnd() {
	return "</form>";
}

function ahref($title, $href, $options = array()) {
	return '<a href="' . URL_PREFIX . strtr($href, ' ', '+') . '"' . _attributes($options) . '>' . $title . '</a>';
}

function partial($name) {
	return App::$response -> render(App::$request -> controller . '/_' . $name);
}

function yield($tpl='default') {
	return App::$response -> storage[$tpl];
}

function render($tpl) {
	return App::$response -> render($tpl);
}

function xss_clean($string) {
	return htmlentities($string, ENT_QUOTES, 'utf-8');
}

function errors_for(&$base) {
	if(empty($base -> errors)) {
		return '';
	} else {
		return '<div class="errors"><div>'. implode('</div><div>', $base -> errors) . '</div></div>';
	}
}

function _attributes($array) {
	$ret = '';

	foreach($array as $id => &$val) {
		$ret .= " $id=\"$val\"";
	}

	return $ret;
}

?>
