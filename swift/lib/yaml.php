<?php

/**
 * Swift framework
 *
 * @author    Swift dev team
 * @copyright Copyright (c) 2010, Swift dev team
 * @license   LICENSE
 * @package   Swift
 */

/**
 * Swift Yaml Class
 *
 * This class is used for parsing yaml and compiling array to yaml
 *
 * @author      Swift dev team
 * @package     Swift
 * @subpackage  Yaml
 */

class Yaml {
	/**
	 * Used for parsing yaml files
	 *
	 * @access public
	 * @param  string $path Path to file
	 * @return object
	 * @static
	 */
	public static function parse($path) {
		if(!file_exists($path)) {
			return array();
		}

		/* Use native if available */
		if(function_exists('yaml_parse_file')) {
			return ($ret = yaml_parse_file($path)) ? $ret : array();
		} else {
			App::load('vendor', 'spyc');
			return Spyc::YAMLLoad($path);
		}
	}

	public static function write($path, $array) {
		if(!file_exists($path)) {
			touch($path);
		}

		if(function_exists('yaml_emit_file') && false) { // it isn't implemented yet
			yaml_emit_file($path, $array);
		} else {
			App::load('vendor', 'spyc');
			file_put_contents($path, Spyc::YAMLDump($array));
		}
	}
}

class YamlException extends Exception {}

?>
