<?php

$constants = array (
			'LOG_DIR' => DIR . '/log/',
			'CONFIG_DIR' => DIR . '/config/',
			'APP_DIR' => DIR . '/app/',
			'PUBLIC_DIR' => DIR . '/public/',
			'TMP_DIR' => DIR . '/tmp',
			'CONTROLLERS_DIR' => DIR . '/app/controllers/',
			'MODEL_DIR' => DIR . '/app/models/',
			'VIEWS_DIR' => DIR . '/app/views/',
			'LOG_ERROR' => 'error', // ?
		);
		
if( !extension_loaded( 'apc' ) ) {
	foreach( $constants as $id => $val )
		define( $id, $val ); 
} else {
	apc_load_constants( 'PRF' . DIR );

	if( !defined( 'LOG_DIR' ) )
		apc_define_constants( 'PRF' . DIR, $constants );
}

/*
define( "LOG_DIR", DIR . "/log/" );
define( "CONFIG_DIR", DIR . "/config/" );
define( "APP_DIR", DIR . "/app/" );
define( "PUBLIC_DIR", DIR . "/public/" );
define( "TMP_DIR", DIR . "/tmp/" );

define( "CONTROLLERS_DIR", APP_DIR . "controllers/" );
define( "MODEL_DIR", APP_DIR . "models/" );
define( "VIEWS_DIR", APP_DIR . "views/" );

define( "LOG_ERROR", "error" );
*/
?>
