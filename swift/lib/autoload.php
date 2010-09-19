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
 */

function __autoload($name) {
	App::load('library', $name);
}

?>
