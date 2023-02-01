<?php 
namespace Core;

use Core\Event;

class ShortCode {
	public static function register($name, $callback) {
		Event::register($name, $callback, 'shortcodes');
		return true;
	}

	public static function fire($name, $params = array()) {
		Event::fire($name, $params, 'shortcodes');
		return true;
	}

	public static function getAll() {
		return Event::getAll('shortcodes');
	}
}