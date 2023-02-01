<?php 
namespace Core;

class Session {
	public static function exists($name) {
		return isset($_SESSION[$name]);
	}
	public static function set($name, $value) {
		return $_SESSION[$name] = $value;
	}
	public static function get($name) {
		return (self::exists($name)) ? $_SESSION[$name] : false;
	}
	public static function delete($name) {
		if(self::exists($name)) {
			unset($_SESSION[$name]);
			return true;
		}
		return false;
	}

	public static function uagent_no_version() {
		$uagent = $_SERVER['HTTP_USER_AGENT'];
		$regx = '/\/[a-zA-Z0-9.]+/';
		$newString = preg_replace($regx, '', $uagent);
		return $newString;
	}
}