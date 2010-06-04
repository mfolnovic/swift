<?php

// example controller

class UsersController extends ControllerBase {
	function index() {
		global $users;
		
		$users = User();
//		$users -> where( array( 'username' => 'mfolnovic' ) ) -> username = 'mfolnovich';
	}
}

?>
