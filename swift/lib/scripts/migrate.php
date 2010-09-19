<?php

class MigrateScript extends Base {
	public static function run() {
		$migrations = self::getMigrations();
		$number     = self::getMigrationNumber();
		$end        = 0;

		foreach($migrations as $migration) {
			$end = max((int) $migration, $end);

			if((int) $migration > $number) {
				$class_name = underscoreToCamelCase(filename(substr($migration,strlen((int) $migration) + 1)));

				include MIGRATIONS_DIR . $migration;
				$instance = new $class_name;

				echo "Running migration $class_name" . PHP_EOL;
				$instance -> up();
			}
		}

		file_put_contents(MIGRATIONS_DIR . '.migration', $end);
		exit;
	}

	public static function getMigrationNumber() {
		return (int) file_get_contents(MIGRATIONS_DIR . '.migration');
	}

	public static function getMigrations() {
		$ret = Dir::files(MIGRATIONS_DIR);
		natsort($ret);
		return $ret;
	}
}

?>
