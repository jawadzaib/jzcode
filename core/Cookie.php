<?php 
namespace Core;

class Cookie {
	public static function exists($name) {
		return isset($_COOKIE[$name]);
	}
	public static function set($name, $value, $expiry) {
		return setcookie($name, $value, time()+$expiry, '/');
	}
	public static function get($name) {
		return (self::exists($name)) ? $_COOKIE[$name] : false;
	}
	public static function delete($name) {
		return (self::exists($name)) ? self::set($name, '', time()-1) : false;
	}
}