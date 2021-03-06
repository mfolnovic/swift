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
 * Swift Database Class - LDAP
 *
 * Gives LDAP functionalities to Swift
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Database
 */

class Db_Ldap extends Base {
	/**
	 * Connection to LDAP
	 */
	var $conn = NULL;
	/**
	 * Options in configuration file
	 */
	var $options;
	/**
	 * Cache instance use internally
	 */
	var $cache;

	/**
	 * Constructor
	 * @access public
	 * @param  array options Options from configuration file
	 * @return void
	 */
	public function __construct($options) {
		$this -> options = $options;
		$this -> cache   = Cache::factory($this -> options['cache_store']);
	}

	/**
	 * Destructor - Disconnects from LDAP
	 *
	 * @access public
	 * @return void
	 */
	public function __destruct() {
		if(!empty($this -> conn)) {
			ldap_close($this -> conn);
		}
	}

	/**
	 * Connects to LDAP and binds as admin
	 *
	 * @access public
	 * @return void
	 * @see bindAdmin
	 */
	public function connect() {
		if(!empty($this -> conn)) {
			return;
		}

		$this -> conn = @ldap_connect($this -> options['host'], $this -> options['port']);

		if($this -> conn === FALSE) {
			throw new LdapException("Failed to connect to LDAP server {$this -> options['host']}:{$this -> options['port']}!");
		}

		ldap_set_option($this -> conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this -> conn, LDAP_OPT_REFERRALS, 0);
		$this -> bindAdmin();
	}

	/**
	 * Binds as username/passwords specified in config
	 *
	 * @access public
	 * @return void
	 */
	public function bindAdmin() {
		if(@ldap_bind($this -> conn, $this -> options['username'], $this -> options['password']) === FALSE) {
			throw new LdapException("Ldap bind as admin was unsuccessful!", ERROR);
		}
	}

	/**
	 * Queries LDAP with conditions based on current relation
	 *
	 * @access public
	 * @param  object $base Model
	 * @return void
	 */
	public function select(&$base) {
		$table             =& Model::instance() -> tables[$base -> tableName];
		$count             =  count($table);
		$base -> resultSet =  array();

		$q = $this -> toQuery($base);
		$im = implode('|', $q);

		Benchmark::start('query');
		$entries = $this -> cache -> get($im) && false;

		if($entries === false) {
			if(!$this -> conn) {
				$this -> connect();
			}

			$res = @ldap_search($this -> conn, $q[0], $q[1], $base -> relation['select']);

			if($res !== false) {
				$rows = @ldap_get_entries($this -> conn, $res);

				for($i = 0; $i < $rows['count']; ++ $i) {
					$entry = array();
					foreach($rows[$i] as $id => $val) {
						if(!is_numeric($id) && $id != 'count') {
							if($val['count'] == 1) {
								$val = $val[0];
							} else if(is_array($val)) {
								array_shift($val);
							}
						
							$entry[$id] = $val;
						}
					}

					$base[] = $entry;
				}
			}
		} else {
			foreach($entries as $id => $val) {
				$base[] = $val;
			}
		}

		Log::write($im, 'LDAP', 'query');
		$this -> cache -> set($im, $base -> resultSet, $this -> options['cache']);
	}

	/**
	 * Generates conditions
	 *
	 * @access public
	 * @param  object $base Model
	 * @return string
	 */
	public function toQuery(&$base, $implode = FALSE) {
		if(empty($base -> relation['basedn'])) {
			$basedn = $this -> options['dn'];
		} else {
			$basedn = $base -> relation['basedn'][0];
		}

		if(empty($base -> relation['where'])) {
			return array($basedn, '(cn=*)');
		}

		$where = '';
		$cnt   = 0;

		foreach($base -> relation['where'] as $field => $value) {
			if(!is_array($value)) {
				$value = array($value);
			}

			$cnt += count($value);

			foreach($value as $val) {
				if(is_numeric($field)) {
					$where .= "($val)";
				} else {
					$where .= "($field=$val)";
				}
			}
		}

		if($cnt > 1) {
			$where = "(|$where)";
		}

		$ret = array($basedn, $where);
		if($implode) {
			$ret = implode('|', $ret);
		}
		return $ret;
	}

	/**
	 * Saves changes to LDAP
	 *
	 * @access public
	 * @param  string $base Model
	 * @return void
	 */
	public function save(&$base) {
		if(!$this -> conn) {
			$this -> connect();
		}

		if(!$base -> relationChanged) {
		} else {
			$this -> select($base);
			$this -> cache -> delete($this -> toQuery($base, true));

			if(!$this -> conn) {
				$this -> connect();
			}
			$this -> bindAdmin();

			foreach($base -> update as $id => $val) {
				if($id == 'unicodePwd') {
					$val = $this -> encryptPassword($val);
				}

				if(empty($val)) {
					@ldap_mod_del($this -> conn, $base -> dn, array($id => array()));
				} else {
					@ldap_mod_add($this -> conn, $base -> dn, array($id => $val));

					if(ldap_errno($this -> conn) > 0) {
						ldap_mod_replace($this -> conn, $base -> dn, array($id => $val));
					}
				}
			}

			$base -> resultSet = array();
		}
	}

	/**
	 * Tries to authenticate with username and password in $data, and returns TRUE if it succeeded, FALSE if it didn't
	 *
	 * @access public
	 * @param  string $base Model
	 * @param  array  $data Username and password
	 * @return bool
	 */
	public function authenticate(&$base, $data) {
		if(!$this -> conn) $this -> connect();

		$ret = !empty($data[0]) && !empty($data[1]) && @ldap_bind($this -> conn, $data[0] . '@' . $this -> options['domain'], $data[1]) == true;

		/** 
		 * Workaround
		 */
		ldap_unbind($this -> conn); 
		unset($this -> conn);
		$this -> connect();

		return $ret;
	}

	/**
	 * Encrypt password
	 *
	 * @access  public
	 * @param   string $password Password to encrypt
	 * @return  string
	 */
	public function encryptPassword($password) {
		$ret = '';

		for($i = 0, $len = strlen($password); $i < $len; ++ $i) {
			$ret .= "{$password{$i}}\000";
		}

		return $ret;
	}

}

class LdapException extends Exception {}
?>
