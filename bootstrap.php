<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname( __FILE__ ));


require_once ROOT.DS."vendor".DS."autoload.php";


// load configuration and helper functions
require_once (ROOT.DS.'config'.DS.'constants.php');


// ERROR Reporting
if(defined('DEBUG') && DEBUG) {
	error_reporting(-1);
	ini_set('display_errors', 1);
} else {
	ini_set('display_errors', 0);
	if (version_compare(PHP_VERSION, '5.3', '>=')) {
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
	} else {
		error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
	}
}


$config = $autoload = [];
require_once (ROOT.DS.'config'.DS.'config.php');
require_once (ROOT.DS.'config'.DS.'autoload.php');
if(isset($autoload['helpers']) && !empty($autoload['helpers'])) {
	foreach ($autoload['helpers'] as $helper) {
		require_once (ROOT.DS.'app'.DS.'lib'.DS.'helpers'.DS.$helper.'.php');
	}
}




$isDevMode = true;
$_config = Setup::createAnnotationMetadataConfiguration(array(ROOT.DS."app".DS."models"), $isDevMode);
// or if you prefer yaml or XML
// $_config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
// $_config = Setup::createYAMLMetadataConfiguration(array(ROOT.DS."config".DS."yaml"), $isDevMode);


// the connection configuration (mysql BASED)
$_dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => DB_USER,
    'password' => DB_PASSWORD,
    'dbname'   => DB_NAME
);

// obtaining the entity manager
$entityManager = EntityManager::create($_dbParams, $_config);


// Autoload Classes
function autoload($className) {
	$namespace = explode('\\', $className);
	$namespace = array_reverse($namespace);
	$className = $namespace[0];
	array_shift($namespace);
	if(!empty($namespace)) {
		foreach ($namespace as &$value) {
			$value = preg_split('/(?=[A-Z])/', $value, -1, PREG_SPLIT_NO_EMPTY);
			$value = strtolower(implode('_', $value));
		}
	}
	$namespace = implode('\\', array_reverse($namespace));
	
	if(file_exists(ROOT.DS.$namespace.DS.$className.'.php')) {
		require_once (ROOT.DS.$namespace.DS.$className.'.php');
	}
}
spl_autoload_register('autoload');
