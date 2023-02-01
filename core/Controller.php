<?php 
namespace Core;

use Core\Application;
use Core\View;
use Core\Configuration;
use Core\RepositoryManager;

class Controller extends Application {
	protected $_controller, $_action;
	public $view;
	public $config;
	public $entityManager;
	public $module;

	public function __construct($controller, $action, $module = null) {
		parent::__construct();
		$this->_controller = $controller;
		$this->_action = $action;
		$this->view = new View();
		
		$this->config = new Configuration();
		$this->entityManager = RepositoryManager::$entityManager;
		$this->setModule($module);
	}

	public function setModule($module) {
		$this->module = $module;
	}

	public function getRepository($modelName) {
		return RepositoryManager::getRepository($modelName);
	}

	public function isController() {
		return true;
	}

	public function configItem($key) {
		return $this->config->getItem($key);
	}
}