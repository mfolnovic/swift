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
 * Swift Model Class - Validations
 *
 * Responsible for validating fields
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Model
 */

class Model_Validations extends Base {
	var $errors = array();
	/**
	 * Returns TRUE if current row is valid
	 *
	 * @access  public
	 * @return  bool
	 */
	public function valid() {
		foreach($this -> update as $field => &$val) {
			if(!isset($this -> validations[$field])) {
				continue;
			}

			foreach($this -> validations[$field] as $validation) {
				if(!call_user_func(array($this,'validates_' . $validation['rule']), $val, $validation)) {
					$this -> errors[] = $validation['message'];
				}
			}
		}

		return empty($this -> errors);
	}

	/**
	 * Returns TRUE if current row is invalid
	 *
	 * @access  public
	 * @return  bool
	 */
	public function invalid() {
		return !$this -> valid();
	}

	/**
	 * Returns TRUE if $val is not empty
	 *
	 * @access public
	 * @param  string $value Value to validate
	 * @return bool
	 */
	public function validates_required($value, $validation) {
		$value = trim($value);
		return !empty($value);
	}

	public function validates_length($value, $validation) {
		$length = strlen($value);
		if(isset($validation['min']) && $length < $validation['min']) return FALSE;
		if(isset($validation['max']) && $length > $validation['max']) return FALSE;

		return TRUE;
	}
}

?>
