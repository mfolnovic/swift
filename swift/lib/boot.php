<?php

/**
 * Swift
 *
 * @author    Swift dev team
 * @copyright Copyright (c) 2010, Swift dev team
 * @license   LICENSE
 * @package   Swift
 */

// @todo Move this
if(empty($_GET['url'])) $_GET['url'] = '';

include LIB_DIR . "base.php";
include LIB_DIR . "dir.php";
include LIB_DIR . "app.php";
include LIB_DIR . "constants.php";
include LIB_DIR . "autoload.php";
include LIB_DIR . "errors.php";
include LIB_DIR . "helpers.php";

App::load('library', 'config', 'plugins', 'router', 'view', 'security');
App::boot();

Plugins::instance() -> loadPlugins();

Security::instance();

if(ENV_HTTP) {
	// Route
	Router::instance() -> route($_GET['url']);

	// Render
	View::instance() -> render('layouts', View::instance() -> layout);
}

if(ENV_CLI) {
	array_shift($argv);
	Scripts::call($argv);
}

?>
