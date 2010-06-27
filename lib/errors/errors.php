<?php

class Errors extends Base {
	function error( $number, $message, $file, $line ) {
		ob_clean();
		$backtrace = debug_backtrace();
		
		include PUBLIC_DIR . "500.php";
		
		exit;
	}
}

$errors = new Errors;
set_error_handler( array( $errors, 'error' ) );

?>
