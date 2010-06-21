<?php

class ViewHelpersLinks extends ViewHelpersForms {
	function link( $title, $href ) {
		echo '<a href="' . URL_PREFIX . '/' . $href . '">' . $title . '</a>';
	}
}

?>
