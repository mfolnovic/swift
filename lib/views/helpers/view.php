<?php

class ViewHelpersView extends ViewHelpersLinks {
	function partial( $name ) {
		global $view;
		$view -> render( null, '_' . $name );
	}
}

?>
