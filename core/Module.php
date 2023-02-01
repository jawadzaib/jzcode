<?php 
namespace Core;

use Core\Module;
use Core\Hooks;
use Core\View;

class Module {
	private static $instance;
	public $name, $namespace;
	private $_config, $_dir, $_url;
	public $view;
	public function __construct() {
	}

	private function autoload_module_class($className) {
		if($this->name) {
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
			$className = str_replace('\\', DS, $className);
			// $className = array_reverse(explode(DS, $className))[0];
			$modules_dir = ROOT.DS.'modules'.DS.$namespace.DS;
			if(file_exists($modules_dir.$className.'.php')) {
				require_once ($modules_dir.$className.'.php');
			}
		}
	}

	public static function getInstance() {
		if (empty(self::$instance)) {
            self::$instance = new Module();
            self::$instance->modules = [];
        }
        return self::$instance;
	}
	public static function register($name, $params = []) {
		$instance = self::getInstance();
		$instance->modules[$name] = $params;
	}
	

	public function setPaths($root) {
		$this->_dir = $root;
	}
	public function setURLs($root) {
		$this->_url = $root;
	}

	public function setConfigurationForModule($config = null) {
		$this->_config = ($config) ? $config : $this->_config;
		$this->namespace = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->name)));
		$this->view = new View();
		$this->view->module = $this;
		spl_autoload_register(array($this, 'autoload_module_class'));
	}

	public function action($controller_name, $action_name) {
		return Router::action($controller_name, $action_name, $this->name);
	}


	protected function registerShortCode($name, $callback) {
		if($name) {
			Hooks::registerShortCode($name, $callback);
		}
	}

	protected function doShortCode($name, $params = array()) {
		if($name) {
			Hooks::fireShortCode($name, $params);
		}
	}

	protected function loadTemplate($viewName, ...$args) {
		$viewArray = explode('/', $viewName);
		$viewString = implode(DS, $viewArray);
		$this->view->loadTemplate($this->_dir.$viewString.'.php');
		// Template::load($this->_dir.$viewString, true);
	}

	public function getConfigItem($name) {
		if(isset($this->_config[$name])) {
			return $this->_config[$name];
		}
		return false;
	}

	protected function registerAction($name, $callback) {
		if($name) {
			Hooks::registerAction($name, $callback);
		}
	}

	protected function fireAction($name, $params) {
		if($name) {
			Hooks::fireAction($name, $params);
		}
	}

	protected function registerScript($id, $path, $is_head = false, $dependencies = []) {
		Hooks::registerScript($id, $path, $is_head, $dependencies, $this->name);
	}
	protected function registerStyle($id, $path, $is_head = false, $dependencies = []) {
		Hooks::registerStyle($id, $path, $dependencies, $this->name);
	}
	public function modulePath($path = '') {
		return $this->_dir.$path;
	}
	public function moduleUrl($path = '') {
		return $this->_url.$path;
	}
}