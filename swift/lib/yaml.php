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
 * @todo        Make our own parser
 * @todo        Compiling array to yaml
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
			throw new YamlException("Path $path doesn't exist!");
		}
	
		/* Use native if available */
		if(function_exists('yaml_parse_file')) {
			return yaml_parse_file($path);
		} else {
			App::load('vendor', 'spyc');
			return spyc_load_file($path);
		}
	}
}

class YamlException extends Exception {}

?>
