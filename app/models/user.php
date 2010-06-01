<?php

// this is an example for model

class User extends Model {
	// here, you make any relationships etc.
	function init() {
		$this -> validates_format_of( 'username', '/[a-zA-Z]/' );
	}
}

?>
