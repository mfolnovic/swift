<?php

class ApplicationController extends ControllerBase {
	var $before_filter = array( 'check_user' );

	function check_user() {
		// here, you could check user, and it would run on every single request
	}	
}

?>
