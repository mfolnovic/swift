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
 * Tests if this request was requested by ajax
 * Currently only works with javascripts frameworks (jquery tested only)
 *
 * @return bool
 * @todo   Doesn't work while file upload using malsup form plugin (plugin bug?)
 */
function isAjax() {
	return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Returns same prefix from $str1 and $str2
 *
 * @param  string $str1 First string
 * @param  string $str2 Second string
 * @return string
 * @todo   Benchmark against substr
 */
function samePrefix($str1, $str2) {
	$ret = '';

	for($i = 0, $len1 = strlen($str1), $len2 = strlen($str2); $i < $len1 && $i < $len2 && $str1[$i] == $str2[$i]; ++ $i)
		$ret .= $str1[$i];

	return $ret;
}

/**
 * Removes same prefix of $str1 and $str2 from $str1 and returns it
 *
 * @param  string $str1 First string
 * @param  string $str2 Second string
 * @return string
 */
function removeSamePrefix($str1, $str2) {
	return substr($str1, 0, strlen(samePrefix($str1, $str2)));
}

/**
 * For a given filename, returns file extension
 *
 * @access public
 * @param  string $filename Name of file
 * @return return
 */
function extension($filename) {
	return substr($filename, strlen(filename($filename)) + 1);
}

/**
 * For a given filename, return file name
 *
 * @access  public
 * @param   string $filename Name of file
 * @return  string
 */
function filename($filename) {
	return substr($filename, 0, strpos($filename, '.'));
}

/**
 * Returns all parent classes
 * If Class1 extended Class2, and Class2 extended Class3, then this function would return array('Class1', 'Class2', 'Class3')
 *
 * @access public
 * @param  string $class Name of class
 */
function get_parent_classes($class) {
	$ret = array($class);

	while(($class = get_parent_class($class)) !== FALSE)
		$ret[] = $class;

	return $ret;
}

/**
 * Returns instance of model $name, and can also create new row from data $data
 *
 * @access public
 * @param  string $name Name of model
 * @return object
*/
function model($name) {
	return Model::instance() -> create($name);
}

?>
