<?php 
namespace Core;

use Core\Event;
use Core\ShortCode;
use Core\Scripts;

class Hooks {
	/*
	// Hooks
	- register_scripts
	*/
	public static function registerAction($name, $callback) {
		Event::register($name, $callback, 'actions');
	}
	public static function fireAction($name, $params = null){
		Event::fire($name, $params, 'actions');
	}

	public static function registerFilter($name, $callback) {
		Event::register($name, $callback, 'filters');
	}
	public static function fireFilter($name){
		Event::fire($name, array(), 'filters');
	}

	public static function registerShortCode($name, $callback) {
		ShortCode::register($name, $callback);
	}

	public static function fireShortCode($name, $params) {
		ShortCode::fire($name, $params);
	}	

	public static function getAll($hook_name) {
		$actions = Event::getAll('actions');
		return isset($actions[$hook_name]) ? $actions[$hook_name] : [];
	}

	public static function registerScript($id, $path, $is_head = false, $dependencies = [], $module = null) {
		$action_name = ($is_head) ? 'head_scripts' : 'footer_scripts';
		Scripts::addItem($action_name, $id, $path, $dependencies, $module);
	}
	public static function registerStyle($id, $path, $dependencies = [], $module = null) {
		Scripts::addItem('stylesheets', $id, $path, $dependencies, $module);
	}

	public static function getScripts($is_head = false) {
		return Scripts::getAll(($is_head) ? 'head_scripts' : 'footer_scripts');
	}
	public static function displayScripts($is_head = false, $module = null) {
		Scripts::displayItems(($is_head) ? 'head_scripts' : 'footer_scripts', $module);
	}
	public static function getStyles() {
		return Scripts::getAll('stylesheets');
	}
	public static function displayStyles($module = null) {
		Scripts::displayItems('stylesheets', $module);
	}
}