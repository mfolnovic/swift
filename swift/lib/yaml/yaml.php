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
	 * @access public
	 * @param  string $path Path to file
	 * @return object
	 * @static
	 */
	static function parse( $path ) {
		return yaml_parse_file( $path );
	}
}

?>
