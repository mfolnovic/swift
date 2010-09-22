<?php

class MigrateScript extends Base {
	public static function run() {
		$instance   = Db_Migrations::instance();
		$migrations = $instance -> getMigrations();
		$number     = $instance -> getMigrationNumber();

		foreach($migrations as $migration) {
			$last = (int) $migration;
			if($last > $number) {
				echo "Running migration " . $instance -> getName($migration) . PHP_EOL;
				$instance -> run($migration);
			}
		}

		file_put_contents(MIGRATIONS_DIR . '.migration', $last);
		$instance -> writeSchema();
	}
}

?>
