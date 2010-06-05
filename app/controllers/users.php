<?php

// example controller

class UsersController extends ControllerBase {
	function index() {
		global $users;
		
		$this -> users = User();
		$this -> users -> where( array( 'username' => 'mfolnovic' ) ) -> username = 'mfolnovich';
	}
}

?>
