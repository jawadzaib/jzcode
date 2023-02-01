<?php
namespace Core; 

class Template {
	private static $instance;
	private static $root_dir = ROOT.DS.'app'.DS;
	private static $path, $complete_path;
    public static function getInstance($path = null, $complete_path = false){
        if ($path != null) {
            self::$instance = new Template();
            self::$instance->path = $path;
            self::$instance->complete_path = $complete_path;
        }
        return self::$instance;
    }
	private static function setPath($path, $complete_path) {
		$viewArray = explode('/', $path);
		$path = implode(DS, $viewArray);
		if(!$complete_path) {
			$path = self::$root_dir.$path;
		}
		$path .= '.php';
		return $path;
	}
	public static function exists($path, $complete_path = false) {
		$path = self::setPath($path, $complete_path);
		if(file_exists($path)) {
			return $path;
		} else {
			return false;
		}

	}
	public static function load($path = null, $complete_path = false) {
		$path = self::exists($path, $complete_path);
		if($path) {
			include $path;
		}
	}
}