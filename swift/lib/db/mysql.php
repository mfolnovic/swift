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
 * Gives Mysql functionalities to Swift
 *
 * @author     Swift dev team
 * @package    Swift
 * @subpackage Database
 */

class Db_Mysql extends Base {
	var $conn       = NULL;
	var $connected  = FALSE;
	var $last_query = NULL;
	var $numrows    = 0;
	var $options;

	/**
	 * Constructor
	 *
	 * @access public
	 * @param  string $options Options from configuration file
	 * @return void
	 */
	public function __construct($options) {
		$this -> options = $options;
	}

	/**
	 * Destructor 
	 * Disconnects from mysql database
	 *
	 * @access public
	 * @return void
	 */
	public function __destruct() {
		if($this -> connected) {
			$this -> conn -> close();
		}
	}

	/**
	 * Secures string from sql injection
	 *
	 * @access public
	 * @param  string $string String to secure
	 * @return string
	 */
	public function safe($string) {
		if(is_numeric($string)) {
			return $string;
		}

		if(empty($string)) {
			return "''";
		}

		if($string instanceof Model_Type_Timestamp) {
			$string = $string -> toDatabase();
		}

		if($string[0] == '`') {
			return $string;
		}

		if(!$this -> conn) {
			$this -> connect();
		}

		if(!is_numeric($string)) {
			return  "'" . mysqli_real_escape_string($this -> conn, $string) . "'";
		}
	}

	/**
	 * Connects to mysql database
	 *
	 * @access public
	 * @return void
	 */
	public function connect() {
		$this -> conn = @new mysqli($this -> options['host'], $this -> options['username'], $this -> options['password'], $this -> options['database']);

		if($this -> conn -> connect_error) {
			trigger_error("Couldn't connect to mysql server <i>{$this -> options['host']}</i> or database <i>{$this -> options['database']}</i> doesn't exist!", ERROR);
		} else {
			$this -> connected = TRUE;
		}

		$this -> conn -> set_charset('utf8');
	}

	/**
	 * Runs query to mysql database
	 *
	 * @access public
	 * @param  string $query Query to run
	 * @return resource
	 */
	public function query($query) {
		if(!$this -> conn) {
			$this -> connect();
		}

		Benchmark::start('query');

		$resource = $this -> conn -> query($query);

		if($resource === FALSE) {
			trigger_error("SQL Error: $query", ERROR);
		}

		Log::write($query, 'MySQL', 'query');

		return $resource;
	}

	public function count(&$base) {
		$res = $this -> query("SELECT COUNT(*) as count FROM {$base -> tableName}" . $this -> generateWhere($base)) -> fetch_assoc();

		return $res['count'];
	}

	/**
	 * Generates where part of query based on current relation
	 *
	 * @access public
	 * @param  object $base Model
	 */
	public function generateWhere(&$base) {
		$ret = '';

		foreach($base -> relation['where'] as $id => $val) {
			$ret .= ($ret == '' ? ' WHERE ' : ' AND ') . (is_numeric($id) ? $val : '`' . $id . '`' . $this -> value($val));
		}

		return $ret;
	}

	/**
	 * Generates parts of query: limit, groupby, order
	 *
	 * @access public
	 * @param  object $base Model
	 * @todo   Implement it
	 */
	public function generateExtra(&$base) {
		$relation =& $base -> relation;

		return (empty($relation['group']) ? '' : ' GROUP BY ' . implode(',', $relation['group'])) .
		       (empty($relation['limit']) ? '' : ' LIMIT ' . implode(',', $relation['limit']));
	}

	/**
	 * Generates joins
	 *
	 * @access public
	 * @param  object $base Model
	 * @return return
	 */
	public function generateJoins(&$base) {
		$ret  =  '';
		$join =& $base -> relation['join'];

		for($i = 0, $size = count($join); $i < $size; $i += 2) {
			$ret .= " LEFT JOIN {$join[$i]} ON {$join[$i + 1]}";
		}

		return $ret;
	}

	/**
	 * Does query based on current relation
	 * @access public
	 * @param  object $base Model
	 * @return void
	*/
	public function select(&$base) {
		if($base -> relationChanged === FALSE) {
			return;
		}

		$base -> resultSet       =  array();
		$base -> relationChanged =  FALSE;
		$table                   =& Model::instance() -> tables[$base -> tableName];
		$res                     = $this -> query($this -> toQuery($base));

		for($i = 0; $i < $res -> num_rows; ++ $i) {
			$row                           =  $res -> fetch_assoc();
			$table[$row['id']]             =  new Model_Row(get_class($base), $row);
			$base -> resultSet[$row['id']] =& $table[$row['id']];
		}
		
		$base -> handleAssociations();
	}

	public function toQuery(&$base) {
		$relation =& $base -> relation;

		if(empty($relation['select'])) {
			$select = '*';
		} else {
			$select = (!empty($relation['join']) ? '' : 'id,') . implode(',', $relation['select']);
		}

		return "SELECT " . $select . " FROM " . $base -> tableName . $this -> generateJoins($base) . $this -> generateWhere($base) . $this -> generateExtra($base) . ';';
	}

	/**
	 * Saves changes to Mysql
	 *
	 * @access public
	 * @param  object $base Model
	 * @return void
	 */
	public function save(&$base) {
		if($base -> invalid()) {
			return;
		}

		if(!$base -> relationChanged) {
			$columns   =  '';
			$values    =  '';
			$newRecord =& $base -> resultSet[-1];

			foreach($newRecord as $id => $val) {
				if(!empty($columns)) {
					$columns  .= ',';
					$values   .= ',';
				}

				$columns .= "`$id`";
				$values  .= $this -> safe($val);
			}

			$this -> query("INSERT INTO {$base -> tableName} ($columns) VALUES ($values)");
			$newRecord -> id         = $this -> conn -> insert_id;
			$base -> relationChanged = true;
			$base -> where(array('id' => $newRecord -> id));

			$table                               =& Model::instance() -> tables[$base -> tableName];
			$table[$newRecord -> id]             =  $base -> newRecord;
			$base -> resultSet[$newRecord -> id] =& $table[$newRecord -> id];
			$base -> update                      = array();

			unset($newRecord);
		} else {
			$set = '';

			foreach($base -> update as $id => $val) {
				$set .= ($set == '' ? '' : ',') . "`$id`=" . $this -> safe($val);
			}

			$this -> query("UPDATE {$base -> tableName} SET $set " . $this -> generateWhere($base));
			$base -> update = array();
		}
	}

	/**
	 * Deletes row
	 *
	 * @access public
	 * @param  string $base Model
	 * @return void
	 */
	public function delete(&$base) {
		$this -> query("DELETE FROM {$base -> tableName}" . $this -> generateWhere($base));
	}

	public function recreateTable(&$base) {
		$this -> query("DROP TABLE IF EXISTS " . $base -> tableName);

		$q     = "CREATE TABLE " . $base -> tableName . " (";
		$first = true;

		foreach($base -> schema as $field => $desc) {
			if(!$first) {
				$q .= ',';
			}

			$q .= '`' . $field . '` ' . $desc['type'];
			if(isset($desc['size'])) {
				$q .= '(' . $desc['size'] . ')';
			}

			if(isset($desc['default'])) {
				$q .= ' DEFAULT ' . $desc['default'];
			}

			if(isset($desc['auto_increment'])) {
				$q .= ' AUTO_INCREMENT';
			}

			$first = FALSE;
		}

		/**
		 * @todo add support for other types of keys
		 */
		foreach($base -> schema_keys as $field => $type) {
			$q .= ',';

			if($type == 'primary') {
				$q .= "PRIMARY KEY (`$field`)";
			}

			$first = FALSE;
		}
		$q .= ') ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;';
		
		$this -> query($q);
	}

	/**
	 * Generates values based on type of object
	 * <code>
	 * value('123') => " = '123'"
	 * value("'hello'") => " = \'hello\'"
	 * value(array(1, 2, 3)) => " IN (1,2,3)"
	 * </code>
	 *
	 * @access public
	 * @param  string $object Object
	 * @return return
	 * @todo   Merge with safe (?)
	 */
	protected function value($object) {
		if(!is_array($object)) {
			return " = " . $this -> safe($object);
		} else {
			return " IN (" . implode(',', $object) . ")";
		}
	}
}

?>
