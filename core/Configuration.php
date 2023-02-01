<?php 
namespace Core;

class Configuration {
	private $_menus, $_config;
	public function __construct() {
		$this->_menus = json_decode(file_get_contents(ROOT.DS.'config'.DS.'menus.json'));
		global $config;
		$this->_config = $config;
	}
	public function getItem($key) {
		return isset($this->_config[$key]) ? $this->_config[$key] : null;
	}
	public function setItem($key, $value) {
		$this->_config[$key] = $value;
		return $this;
	}

	public function getMenu($name) {
		return (isset($this->_menus->{$name})) ? $this->_menus->{$name} : null;
	}
}