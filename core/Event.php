<?php 
namespace Core;

use Core\Event;

class Event {
    private static $instance;

    public static function getInstance(){
        if (empty(self::$instance)) {
            self::$instance = new Event();
        }
        return self::$instance;
    }

    public static function register($name, $callback, $type = 'events') {
        self::$instance->{$type}[$name][] = $callback;
    }
    public static function fire($name, $params = null, $type = 'events'){
        $instance = self::getInstance();
        if (isset($instance->{$type}[$name])) {
            foreach ($instance->{$type}[$name] as $fn) {
                call_user_func_array($fn, array(&$params));
            }
        }
    }
    public static function getAll($type = 'events') {
        $instance = self::getInstance();
        if (isset($instance->{$type})) {
            return $instance->{$type};
        }
        return null;
    }
    public static function remove($name, $type = 'events'){
        $instance = self::getInstance();
        unset($instance->{$type}[$name]);
    }
}