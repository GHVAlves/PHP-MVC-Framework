<?php

namespace Index {

	use \System\Core\Bootstrap;

	/**
	 * ROOT PATH
	 */

	define('ROOT', dirname(__FILE__));

	/**
	 * SYSTEM PATH NAME
	 */

	define('SYSTEM_PATH', 'system');

	/**
	 * APPLICATION FOLDER NAME
	 */

	define('APPLICATION_FOLDER', 'application');

	/**
	 * ENVIROMENT TYPE
	 *
	 * development - Show all errors;
	 * production - Hide all errors;
	 */

	define('ENVIRONMENT', 'development');

	/**
	 * Autoload
	 */

	require_once ROOT . DIRECTORY_SEPARATOR . SYSTEM_PATH . '/core/Autoload.php';

	/**
	 * Stating System
	 */

	$bootstrap = new Bootstrap();
	$bootstrap->run();

}

?>
