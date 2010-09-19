<?php

/**
 * Swift
 *
 * @author    Swift dev team
 * @copyright Copyright (c) 2010, Swift dev team
 * @license   LICENSE
 * @package   Swift
 */

/**
 * Autoload function
 *
 * @param  string name Name of class
 * @return bool
 * @todo   use multiple autoload for plugins
 * @todo   try to automate loading singleton
 */

function __autoload($name) {
	App::load('library', $name);
}

?>
