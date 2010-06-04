<?php

require "model/base.php";

class Model {
	var $tables = array();

	function initTable( $table) {
		if( !isset( $this -> tables[ $table ] ) )
			$this -> tables[ $table ] = new ModelTable();
	}
};

$model = new Model;

?>
