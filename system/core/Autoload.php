<?php

spl_autoload_register(function ($className) {

	$className = $className . '.php';
	
	if (file_exists(ROOT . DIRECTORY_SEPARATOR . APPLICATION_FOLDER . DIRECTORY_SEPARATOR . $className)) {

		require_once ROOT . DIRECTORY_SEPARATOR . APPLICATION_FOLDER . DIRECTORY_SEPARATOR .  $className;

	}
	else if (file_exists(ROOT . DIRECTORY_SEPARATOR . $className)) {

		require_once ROOT . DIRECTORY_SEPARATOR . $className;

	}
	else {

		die("Class '$className' not found.");

	}

});

if (file_exists(ROOT . DIRECTORY_SEPARATOR . SYSTEM_PATH . '/libraries/autoload.php')) {

	require_once ROOT . DIRECTORY_SEPARATOR . SYSTEM_PATH . '/libraries/autoload.php';

}

?>
