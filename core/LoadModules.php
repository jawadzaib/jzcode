<?php 
namespace Core;

use Core\Module;

class LoadModules {
	private static $_generated_modules = [];
	public static function generate() {
		if(file_exists(ROOT.DS.'config'.DS.'modules.php')) {
			require_once (ROOT.DS.'config'.DS.'modules.php');
		}
		$modules = Module::getInstance()->modules;
		if(!empty($modules)) {
			foreach ($modules as $name => $module) {
				if(!isset($module['active']) || $module['active'] === true) {
					self::generateModule($name);
				} 
			}
		}
	}
	
	public static function path($name) {
		return ROOT.DS.'modules'.DS.$name.DS;
	}
	public static function getModule($name) {
		if(isset(self::$_generated_modules[$name])) {
			return self::$_generated_modules[$name];
		} else {
			return false;
		}
	}
	private static function generateModule($name) {
		$className = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
		$file_path = ROOT.DS.'modules'.DS.$name.DS.$className.'.php';
		$className = $className.DS.$className;
		
		if(file_exists($file_path)) {
			require_once ($file_path);
			if(method_exists($className, 'setConfigurationForModule')) {
				$config_path = ROOT.DS.'modules'.DS.$name.DS.'config.php';
				if(file_exists($config_path)) {
					require_once ($config_path);
				}
				$module = new $className();
				$module->setPaths(ROOT.DS.'modules'.DS.$name.DS);
				$module->setURLs(PROOT.'modules/'.$name.'/');
				$module->name = $name;
				$routes_path = ROOT.DS.'modules'.DS.$name.DS.'routes.php';
				if(file_exists($routes_path)) {
					require_once ($routes_path);
				}

				$config = isset($config) ? $config : [];
				$module->setConfigurationForModule($config);
				self::$_generated_modules[$module->name] = $module;
				if(method_exists($className, 'initialize')) {
					$module->initialize();
				}
			} else {
				die("You must need to extend your module `".$className."` class with \Module");
			}
		}
	}

	

}