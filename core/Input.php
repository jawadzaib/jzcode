<?php
namespace Core;

class Input {
	public static function sanitize($value) {
		return htmlentities($value, ENT_QUOTES, 'UTF-8');
	}

	public static function post($key) {
		if(isset($_POST[$key])) {
			return self::sanitize($_POST[$key]);
		}
		return false;
	}
	public static function get($key) {
		if(isset($_GET[$key])) {
			return self::sanitize($_GET[$key]);
		}
		return false;
	}
}