<?php 
namespace Core;

use Core\Event;

class Scripts {
	private static $instance;
	// stylesheets 
	// head_scripts
	// footer_scripts
    public static function getInstance(){
        if (empty(self::$instance)) {
            self::$instance = new Event();
        }
        return self::$instance;
    }

    public static function addItem($name, $id, $path, $dependencies = [], $module = null) {
        if(self::$instance) {
			self::$instance->{$name}[] = array(
				'id' => $id,
				'path' => $path,
				'dependencies' => $dependencies,
				'module' => $module
			);
		}
    }

    public static function getAll($name) {
    	$instance = self::getInstance();
    	return property_exists($instance, $name) ? $instance->{$name} : null;
    }

    public static function displayItems($name, $module = null) {
		$scripts = self::getAll($name);
		if(!empty($scripts)) {
			$added_scripts = [];
			foreach ($scripts as $script) {
				if($module != null && ($script['module'] == null || $script['module'] !== $module)) {
					continue;
				}
				if($module == null && $script['module'] != null) {
					continue;
				}
				if(isset($script['path'])) {
					$viewArray = explode(DS, $script['path']);
					$script['path'] = implode('/', $viewArray);
					// if(isset($script['dependencies']) && !empty)
					if($name == 'stylesheets') {
						echo '<link href="'.$script['path'].'" rel="stylesheet" type="text/css">';
					} else {	
						echo '<script type="text/javascript" src="'.$script['path'].'"></script>';
					}
					array_push($added_scripts, $script['id']);
				}
			}
		}
	}
}