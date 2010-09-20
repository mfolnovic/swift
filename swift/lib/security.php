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
 * Swift Security Class
 *
 * This class is responsible for filtering data from XSS, checking if it's CSRF attack etc.
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Security
 */

class Security extends Base {
	/**
	 * Current CSRF token
	 */
	var $csrf_token;
	/**
	 * Time in seconds after which csrf token expires
	 */
	var $token_expiration = 3600;

	/**
	 * Constructor
	 * Filters $_POST and generates CSRF token
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this -> filter($_POST);
		$this -> csrfToken();
	}

	/**
	 * Checks if CSRF token is correct
	 *
	 * @access public
	 * @return void
	 */
	public function checkCsrf() {
		if(empty($_POST)) {
			return;
		}

		$time = time();

		foreach($_SESSION['csrf_tokens'] as $id => $val) {
			if($val[1] - $time > $this -> token_expiration) {
				unset($_SESSION['csrf_tokens'][$id]);
			} else if($val[0] == $_POST['csrf_token']) {
				return;
			}
		}

		Controller::getInstance() -> render404();
	}

	/**
	 * This function generates csrf token
	 *
	 * @access public
	 * @return void
	 */
	public function csrfToken() {
		$this -> csrf_token = md5(mt_rand());

		if(!isset($_SESSION['csrf_tokens'])) {
			$_SESSION['csrf_tokens'] = array();
		}

		array_push($_SESSION["csrf_tokens"], array($this -> csrf_token, time()));
	}

	/**
	 * Fitlers array $array from XSS
	 *
	 * @access public
	 * @param  array $array Array to filter
	 * @return array
	 */
	public function filter(&$array) {
		if(is_string($array)) {
			return htmlentities($array, ENT_COMPAT, 'utf-8');
		}

		foreach($array as $id => &$val)
			$this -> filter($val);
	}

}

?>
