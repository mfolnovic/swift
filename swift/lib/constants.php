<?php

/**
 * Swift
 *
 * @author    Swift dev team
 * @copyright Copyright (c) 2010, Swift dev team
 * @license   LICENSE
 * @package   Swift
 */

/**
 * Provides internal constants
 */

define( 'ENV_DEVELOPMENT', 4 );
define( 'ENV_PRODUCTION', 8 );
define( 'ENV_TEST', 16 );
define( 'ENV_INTERNAL', 32 );

define( 'ENV_CLI', php_sapi_name() == 'cli' );
define( 'ENV_HTTP', !ENV_CLI );

define( 'ERROR', E_USER_ERROR );
define( 'WARNING', E_USER_WARNING );
define( 'NOTICE', E_USER_NOTICE );

define( 'APP_DIR', DIR . 'app/' );
define( 'CONFIG_DIR', APP_DIR . 'config/' );
define( 'CONTROLLERS_DIR', APP_DIR . 'controllers/' );
define( 'LOG_DIR', APP_DIR . 'log/' );
define( 'MODEL_DIR', APP_DIR . 'models/' );
define( 'PLUGIN_DIR', APP_DIR . 'plugins/' );
define( 'PUBLIC_DIR', APP_DIR . 'public/' );
define( 'TMP_DIR', APP_DIR . 'tmp/' );
define( 'VIEWS_DIR', APP_DIR . 'views/' );

if( empty( $_SERVER[ 'REQUEST_URI' ] ) ) {
	define( 'URL', '' );
	define( 'FULL_URL', '' );
} else {
	define( 'URL', empty( $_SERVER[ 'REQUEST_URI' ] ) ? '' : $_SERVER[ 'REQUEST_URI' ] );
	define( 'FULL_URL', 'http://'. $_SERVER[ 'SERVER_NAME' ] . URL );
}

define( 'URL_PREFIX', Dir::sameFolders( URL, $_SERVER[ 'PHP_SELF' ] ) );

?>
