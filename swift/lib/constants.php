<?php

/**
 * Swift
 *
 * @package		Swift
 * @author		Swift dev team
 * @copyright	Copyright (c) 2010, Swift dev team
 * @license		LICENSE
 */

/**
 * Provides internal constants
 */

define( 'LOG_ERROR', 'error' ); // ?
define( 'ENV_HTTP', 1 );
define( 'ENV_CLI', 2 );
define( 'ENV_DEVELOPMENT', 4 );
define( 'ENV_PRODUCTION', 8 );
define( 'ENV_TEST', 16 );
define( 'ENV_INTERNAL', 32 );

define( 'APP_DIR', DIR . 'app/' );
define( 'LOG_DIR', APP_DIR . 'log/' );
define( 'CONFIG_DIR', APP_DIR . 'config/' );
define( 'PUBLIC_DIR', APP_DIR . 'public/' );
define( 'TMP_DIR', APP_DIR . 'tmp/' );
define( 'CONTROLLERS_DIR', APP_DIR . 'controllers/' );
define( 'MODEL_DIR', APP_DIR . 'models/' );
define( 'VIEWS_DIR', APP_DIR . 'views/' );

define( "URL_PREFIX", removeSamePrefix( $_SERVER[ "REQUEST_URI" ], $_SERVER[ "PHP_SELF" ] ) );

?>
