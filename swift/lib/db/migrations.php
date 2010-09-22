<?php

class Db_Migrations extends Base {
	var $schema     = array();

	public function __construct() {
		$this -> schema = Yaml::parse(MIGRATIONS_DIR . '.schema');
	}

	public function run($migration) {
		$class_name = $this -> getName($migration);

		include MIGRATIONS_DIR . $migration;
		$instance = new $class_name;
		$instance -> up();
	}

	public function getMigrationNumber() {
		return (int) file_get_contents(MIGRATIONS_DIR . '.migration');
	}

	public function getMigrations() {
		$ret = Dir::files(MIGRATIONS_DIR);
		natsort($ret);

		return $ret;
	}

	public function getName($migration) {
		return underscoreToCamelCase(filename(substr($migration,strlen((int) $migration) + 1)));
	}

	public function addToSchema($table, $schema) {
		if(!isset($this -> schema[$table])) {
			$this -> schema[$table] = array();
		}

		$this -> schema[$table] += $schema;
	}

	public function writeSchema() {
		Yaml::write(MIGRATIONS_DIR . '.schema', $this -> schema);
	}
}

?>
